/*       xpl-teleinfo												*/
/* Version pour PC et wrt54gl											*/
/* Lecture donnees Teleinfo et envoie donnees par message xPL toutes les minutes et si depassement capacite	*/
/* Connexion par le port serie du PC ou du Wrt54gl								*/
/* Verification checksum donnees teleinfo et boucle de 3 essais si erreurs.					*/
/* Par domos78 at free point fr											*/
 
/*
Parametres a adapter: 
- Port serie a modifier en consequence avec SERIALPORT.
- Nombre de valeurs a relever: NB_VALEURS + tableaux "etiquettes" a modifier selon abonnement (ici triphase heures creuses).
 
Compilation PC:  
- gcc -Wall xpl-teleinfo.c -o xpl-teleinfo
 
Compilation wrt54gl: 
- avec le SDK (OpenWrt-SDK-Linux).
  $  make && scp ./build_dir/linux-brcm47xx/xpl-teleinfo/xpl-teleinfo root@wrt54gl:/tmp
*/
 
//-----------------------------------------------------------------------------
#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>
#include <string.h>
#include <time.h>
#include <errno.h>
#include <syslog.h>
#include <termios.h>
#include <signal.h>
#include <sys/fcntl.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <netinet/in.h>
 
// Declaration xPL
#define BROADCASTIP "192.168.0.255"	// Adr. de broadcast sur reseau local.
#define HUBPORT	3865			// Port HUB xPL


#define XPLMSGSOURCE "domoserv-teleinfo.raspberrypi"
#define XPLMSGTARGET "*"
 
// Declaration port serie
#define BAUDRATE B1200
#define SERIALPORT "/dev/ttyUSB0"
 
// Variables socket
int sockxpl ;
struct sockaddr_in address ;
 
// Nom fichier de sauvegarde trame.
#define TRAMELOG "/tmp/teleinfotrame."
 
//-----------------------------------------------------------------------------
 
// Declaration pour le port serie.
int             fdserial ;
struct termios  termiosteleinfo ;
 
// Declaration pour les donnees.
char ch[2] ;
char car_prec ;
char message[512] ;
char* match;
int id ;
char datateleinfo[512] ;
 
// Constantes/Variables a changees suivant abonnement (Nombre de valeurs, voir tableau "etiquettes", 20 pour abonnement tri heures creuse).
#define NB_VALEURS 20
char etiquettes[NB_VALEURS][16] = {"ADCO", "OPTARIF", "ISOUSC", "HCHP", "HCHC", "PTEC", "IINST1", "IINST2", "IINST3", "IMAX1", "IMAX2", "IMAX3", "PMAX", "PAPP", "HHPHC", "MOTDETAT", "PPOT", "ADIR1", "ADIR2" ,"ADIR3"} ;
// Fin Constantes/variables a changees suivant abonnement.
 
char 	valeurs[NB_VALEURS][18] ;
char 	checksum[255] ;
int 	res ;
int	no_essais = 1 ;
int	nb_essais = 3 ;
int	erreur_checksum = 0 ;
int	nophase ;
 
// Declaration pour la date.
time_t 		ts;
struct 	tm 	*dc;
char		timestamp[11];
int		minutes_courantes   = 60;
int		cpt_minutes ;
 
// Declaration gestion programme
int 		finprog ;
int 		debug = 0 ;
 
/*------------------------------------------------------------------------------*/
/* Init port rs232								*/
/*------------------------------------------------------------------------------*/
int initserie(void)
// Mode Non-Canonical Input Processing, Attend 1 caractere ou time-out(avec VMIN et VTIME).
{
	int device ;
 
        // Ouverture de la liaison serie (Nouvelle version de config.)
        if ( (device=open(SERIALPORT, O_RDWR | O_NOCTTY)) == -1 ) 
	{
                syslog(LOG_ERR, "Erreur ouverture du port serie %s !", SERIALPORT);
                exit(1) ;
        }
 
        tcgetattr(device,&termiosteleinfo) ;				// Lecture des parametres courants.
 
	cfsetispeed(&termiosteleinfo, BAUDRATE) ;			// Configure le debit en entree/sortie.
	cfsetospeed(&termiosteleinfo, BAUDRATE) ;
 
	termiosteleinfo.c_cflag |= (CLOCAL | CREAD) ;			// Active reception et mode local.
 
	// Format serie "7E1"
	termiosteleinfo.c_cflag |= PARENB  ;				// Active 7 bits de donnees avec parite pair.
	termiosteleinfo.c_cflag &= ~PARODD ;
	termiosteleinfo.c_cflag &= ~CSTOPB ;
	termiosteleinfo.c_cflag &= ~CSIZE ;
	termiosteleinfo.c_cflag |= CS7 ;
 
	termiosteleinfo.c_iflag |= (INPCK | ISTRIP) ;			// Mode de control de parite.
 
	termiosteleinfo.c_cflag &= ~CRTSCTS ;				// Desactive control de flux materiel.
 
	termiosteleinfo.c_lflag &= ~(ICANON | ECHO | ECHOE | ISIG) ;	// Mode non-canonique (mode raw) sans echo.
 
	termiosteleinfo.c_iflag &= ~(IXON | IXOFF | IXANY | ICRNL) ;	// Desactive control de flux logiciel, conversion 0xOD en 0x0A.
 
	termiosteleinfo.c_oflag &= ~OPOST ;				// Pas de mode de sortie particulier (mode raw).
 
	termiosteleinfo.c_cc[VTIME] = 80 ;  				// time-out a ~8s.
	termiosteleinfo.c_cc[VMIN]  = 0 ;   				// 1 car. attendu.
 
	tcflush(device, TCIFLUSH) ;					// Efface les donnees reçues mais non lues.
        tcsetattr(device,TCSANOW,&termiosteleinfo) ;			// Sauvegarde des nouveaux parametres
	return device ;
}
 
/*------------------------------------------------------------------------------*/
/* Lecture donnees teleinfo sur port serie					*/
/*------------------------------------------------------------------------------*/
void LiTrameSerie(int device)
{
// (0d 03 02 0a => Code fin et debut trame)
	tcflush(device, TCIFLUSH) ;			// Efface les donnees non lus en entree.
	message[0]='\0' ;
	memset(valeurs, 0x00, sizeof(valeurs)) ; 
 
	do
	{
		car_prec = ch[0] ;
		res = read(device, ch, 1) ;
		if (! res)
		{	
			syslog(LOG_ERR, "Erreur pas de reception debut donnees Teleinfo !\n") ;
			close(device);
			exit(1) ;
		}
	 }
	while ( ! (ch[0] == 0x02 && car_prec == 0x03) ) ;	// Attend code fin suivi de debut trame teleinfo .
 
	do
	{
		res = read(device, ch, 1) ;
		if (! res)
		{	
			syslog(LOG_ERR, "Erreur pas de reception fin donnees Teleinfo !\n") ;
			close(device);
			exit(1) ;
		}
		ch[1] ='\0' ;
		strcat(message, ch) ;
	}
	while (ch[0] != 0x03) ;				// Attend code fin trame teleinfo.
}
 
/*------------------------------------------------------------------------------*/
/* Test checksum d'un message (Return 1 si checkum ok)				*/
/*------------------------------------------------------------------------------*/
int checksum_ok(char *etiquette, char *valeur, char checksum) 
{
	unsigned char sum = 32 ;		// Somme des codes ASCII du message + un espace
	int i ;
 
	for (i=0; i < strlen(etiquette); i++) sum = sum + etiquette[i] ;
	for (i=0; i < strlen(valeur); i++) sum = sum + valeur[i] ;
	sum = (sum & 63) + 32 ;
	if ( sum == checksum) return 1 ;	// Return 1 si checkum ok.
	if (debug) syslog(LOG_INFO, "Checksum lu:%02x   calcule:%02x", checksum, sum) ;
	return 0 ;
}
 
/*------------------------------------------------------------------------------*/
/* Recherche valeurs des etiquettes de la liste.				*/
/*------------------------------------------------------------------------------*/
int LitValEtiquettes()
{
	int id ;
	erreur_checksum = 0 ;
 
	for (id=0; id<NB_VALEURS; id++)
	{
		if ( (match = strstr(message, etiquettes[id])) != NULL)
		{
			sscanf(match, "%s %s %s", etiquettes[id], valeurs[id], checksum) ;
			if ( strlen(checksum) > 1 ) checksum[0]=' ' ;	// sscanf ne peux lire le checksum a 0x20 (espace), si longueur checksum > 1 donc c'est un espace.
			if ( ! checksum_ok(etiquettes[id], valeurs[id], checksum[0]) ) 
			{
				syslog(LOG_ERR, "Donnees teleinfo [%s] corrompues (essai %d) !\n", etiquettes[id], no_essais) ;
				erreur_checksum = 1 ;
				return 0 ;
			}
		}
	}
	// Remplace chaine "HP.." ou "HC.." par "HP ou "HC".
	valeurs[5][2] = '\0' ;
	//if (debug) printf("---------- %s ------------\n", timestamp) ; for (id=0; id<NB_VALEURS; id++) printf("%s='%s'\n", etiquettes[id], valeurs[id]) ;
	return 1 ;
}
 
/*------------------------------------------------------------------------------*/
/* Test si depassement intensite						*/
/*------------------------------------------------------------------------------*/
int DepasseCapacite()
{
	//  Test sur les 3 phases (etiquette ADIR1, ADIR2, ADIR3) a remplacer par ADPS pour monophase.
	int nphase ;
	for (nphase=1; nphase<=3; nphase++)
	{
		if ( strlen(valeurs[16 + nphase]) ) 	// Si ADIRn non vide => depassement.
		{
			syslog(LOG_INFO, "Depassement d intensite: ADIR%d='%s' !", nphase, valeurs[16 + nphase]) ;
			return nphase ;
		}
	}
	return 0 ;
}
 
/*------------------------------------------------------------------------------*/
/* Ecrit la trame teleinfo dans fichier si erreur (pour debugger)		*/
/*------------------------------------------------------------------------------*/
void writetrameteleinfo(char trame[], char ts[])
{
	char nomfichier[255] = TRAMELOG ;
	strcat(nomfichier, ts) ;
        FILE *teleinfotrame ;
        if ((teleinfotrame = fopen(nomfichier, "w")) == NULL)
        {
		syslog(LOG_ERR, "Erreur ouverture fichier teleinfotrame %s !", nomfichier) ;
                exit(1);
        }
        fprintf(teleinfotrame, "%s", trame) ;
        fclose(teleinfotrame) ;
}
 
/*------------------------------------------------------------------------------*/
/* Fonctions socket 								*/
/*------------------------------------------------------------------------------*/
int sockxpl_init()
{
	/* Create the UDP socket */
	int socketudp ;
	int enabled = 1 ;
 
	if ((socketudp = socket(AF_INET, SOCK_DGRAM, IPPROTO_UDP)) < 0)
	{
		syslog(LOG_ERR, "Erreur ouverture socket: %s (%d)\n", strerror(errno), errno) ;
		exit(EXIT_FAILURE) ;
	}
 
	setsockopt(socketudp, SOL_SOCKET, SO_BROADCAST, &enabled, sizeof enabled) ; //Droit broadcast.
 
	/* Construct the server sockaddr_in structure */
	memset(&address, 0, sizeof(address));       		/* Clear struct */
	address.sin_family = AF_INET;                  		/* Internet/IP */
	address.sin_addr.s_addr = inet_addr(BROADCASTIP);	/* IP address */
	address.sin_port = htons(HUBPORT);       		/* Hub port */
 
	return socketudp ;
}
 
 
// Broadcast message xpl
int sendxplmsg(char msg[])
{
	/* Send the msg to the server */
	int msglen ;
	msglen = strlen(msg);
	if (debug) printf("%s\n", msg) ;
	if (sendto(sockxpl, msg, msglen, 0, (struct sockaddr *) &address, sizeof(address) ) != msglen)
	{
		syslog(LOG_ERR, "Erreur envoie message xPL: %s (%d)\n", strerror(errno), errno) ;
		return 0 ;
	}
	return 1 ;
}
 
// Broadcast message teleinfo.basic xpl
int sendxplteleinfobasicmsg(char msgtype[],char data[])
{
	char msg[1024] ;
	char msgheader[512] ;
 
	// Entête du message
	sprintf( msgheader, "%s\n{\nhop=1\nsource=%s\ntarget=%s\n}\nteleinfo.basic\n{\n", msgtype, XPLMSGSOURCE, XPLMSGTARGET) ;
 
	sprintf( msg, "%s%s}\n", msgheader, data) ;
	if ( sendxplmsg(msg) ) return 1 ;
		else return 0 ;
}
 
/*------------------------------------------------------------------------------*/
/* Fonctions interruptions     							*/
/*------------------------------------------------------------------------------*/
void signal_handler(int signum)
{
        switch(signum)
        {
                case SIGINT:
                case SIGQUIT:
                case SIGTERM:
			syslog(LOG_INFO, "Abandon programme demande. !") ;
			finprog = 1 ;							// Flag fin boucle.
                        break;
                case SIGHUP:
			syslog(LOG_INFO, "Reactivation programme demandee. !") ;	// Pas d'action
                        break ;
        }
}
 
void init_interruptions_signal(void)
{
	signal(SIGHUP, &signal_handler);		// Intercepte les signaux pour quitter le prog. proprement.
	signal(SIGINT, &signal_handler);		// Fonction 'signal_handler' appelee.
	signal(SIGTERM, &signal_handler);
	signal(SIGQUIT, &signal_handler);
}
 
/*------------------------------------------------------------------------------*/
/* Main										*/
/*------------------------------------------------------------------------------*/
int main(int argc, char *argv[])
{
 
 /* Init. interceptions les signaux */
 init_interruptions_signal() ; 
 
 /* Ouvre fichier log syslog */
 openlog("xpl-teleinfo", LOG_PID, LOG_USER) ;
 syslog(LOG_INFO, "Demarrage ...") ;
 
 /* Init port serie. */
 fdserial = initserie() ;
 
 /* Init socket xpl. */
 sockxpl = sockxpl_init() ;
 
 do 
 {	
	no_essais = 1 ;
 	do
 	{
		// Lit trame teleinfo.
		LiTrameSerie(fdserial) ;
 
		ts = time(0) ;                                     //Lit date/heure systeme.
		dc = localtime(&ts) ;
		strftime(timestamp,sizeof timestamp,"%s",dc);
 
		if ( LitValEtiquettes() ) 			// Lit valeurs des etiquettes de la liste.
		{
			if ( ( nophase=DepasseCapacite() ) ) 		// Test si etiquette depassement intensite.
			{
				sprintf( datateleinfo, "ADIR%d=%s\nADCO=%s\nIINST1=%s\nIINST2=%s\nIINST3=%s\n", 
					nophase, valeurs[16 + nophase], valeurs[0], valeurs[6], valeurs[7], valeurs[8]) ;	
				sendxplteleinfobasicmsg("xpl-trig", datateleinfo) ;		// Envoie data sur reseau xPL.
				writetrameteleinfo(message, timestamp) ;			// Enregistre trame.
			}
			else
			if ( ! (strlen(valeurs[13]) ) )			// Test si valeur PAPP vide (possible apres trames ADIRn).
			{
				sprintf( datateleinfo, "ADCO=%s\nIINST1=%s\nIINST2=%s\nIINST3=%s\n", 
					valeurs[0], valeurs[6], valeurs[7], valeurs[8]) ;	
				sendxplteleinfobasicmsg("xpl-trig", datateleinfo) ;		// Envoie data sur reseau xPL.
				writetrameteleinfo(message, timestamp) ;			// Enregistre trame.
			}
			else
			if ( minutes_courantes != dc -> tm_min )	// Test si une est passee.
			{
				minutes_courantes = dc -> tm_min ;
				sprintf( datateleinfo, "ADCO=%s\nOPTARIF=%s\nISOUSC=%s\nHCHP=%s\nHCHC=%s\nPTEC=%s\nIINST1=%s\nIINST2=%s\nIINST3=%s\nIMAX1=%s\nIMAX2=%s\nIMAX3=%s\nPMAX=%s\nPAPP=%s\nHHPHC=%s\nMOTDETAT=%s\nPPOT=%s\n", 
					valeurs[0], valeurs[1], valeurs[2], valeurs[3], valeurs[4], valeurs[5], valeurs[6], valeurs[7], valeurs[8], valeurs[9], valeurs[10], valeurs[11], valeurs[12], valeurs[13], valeurs[14], valeurs[15], valeurs[16]) ;
				sendxplteleinfobasicmsg("xpl-trig", datateleinfo) ;		// Envoie data sur reseau xPL.
			}
		}
		else 
		{
			writetrameteleinfo(message, timestamp) ;	// Si erreur checksum enregistre trame.
			no_essais++ ;
		}
 	}
 	while ( (erreur_checksum) && (no_essais <= nb_essais) ) ;
 }
 while (! finprog) ;
 
 syslog(LOG_INFO, "Fermeture socket xPL !") ;
 close(sockxpl) ;
 syslog(LOG_INFO, "Fermeture port serie. !") ;
 close(fdserial) ;
 syslog(LOG_INFO, "Fin programme.") ;		 
 closelog() ;
 exit(0) ;
}
