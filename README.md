ANUBIS-altcoin
==============

Fork of the existing ANUBIS project with support for BTC, DOGE, POT, VTC.


You need a working LAMP setup.  It might work with windows, i don't know and i don't care to test.
You need a working CGminer (Kalroth fork is what i'm testing against).
You need the following settings in your .conf or your bat file or however the hell you run cgminer:

     "api-listen" : true,
     "api-mcast-port" : "4028",
     "api-network" : true,
     "api-port" : "4028",
     "api-allow" : "W:10.0.100.29,W:127.0.0.1",

api-allow needs the W: set and the IP should be the IP of the machine that you're running ANUBIS-altcoin on.

Create a MySQL database for ANUBIS-altcoin.

Create a MySQL user/pass for ANUBIS-altcoin.  (running as root is for the weak)

Plug those values in to config.inc.php

Open a browser to wherever you unpacked ANUBIS-altcoin.  http://your.ANUBIS-altcoin.ip.orHOST/ANUBIS-altcoin/directory/if/it/is/not/the/root/

Create a cron script pointing at the wallet_update.php file along the lines of:

     */1 * * * * php /var/www/ANUBIS-TEST/wallet_update.php


The rest is pretty self-explanatory.

All account data is logged to the db for quick retrieval via your browser.  No more slow polls to block explorers and exchanges.

Krezdorn.  do work!

ROADMAP:

1: DONE! Post wallet data to mysql db.  to cron or not to cron, that is the question.

2: DONE! forgot the salt, but fuck it.... Secure login system.  sha-256+salt to session should do the trick.  in the meantime, set up .htaccess or something.

3: Simplify the hardware screens.  too much data and it seems mostly incoherent to most.

4: Revamp pool add/del and all that jazz.

5: Enable multiple exchanges.  Cause fuck cryptsy?

6: DONE! at least for the first two... Put proper symbol next to currency..  the fancy B for BTC, D for DOGE, etc..  this one might not scale too easy, so many damn alt-coins.

7: General beautification.

8: Support for Litecoin and others...

DMJWnz4YkpyqVUUMM2GNbt6FmjfkcgnM58  /me loves the D...  I promise to share with Krezdorn if we ever manage to net a donation.
