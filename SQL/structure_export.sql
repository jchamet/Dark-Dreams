--
-- MySQL 5.1.72
-- Sat, 18 Jan 2014 17:14:35 +0000
--

CREATE TABLE `arena` (
   `id` int(11) not null auto_increment,
   `ownerid` int(11) not null,
   `validreg` int(11) not null default '0',
   `wins` int(11) not null default '0',
   `losses` int(11) not null default '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`ownerid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=82;


CREATE TABLE `chars` (
   `id` int(11) not null auto_increment,
   `ownerid` int(11) not null,
   `name` varchar(255) not null,
   `creation` datetime not null,
   `image` varchar(255) not null default 'http://i68.servimg.com/u/f68/14/98/12/95/zero110.jpg',
   `level` int(11) not null default '1',
   `health` int(11) not null default '100',
   `experience` int(11) not null default '0',
   `gold` int(11) not null default '25',
   `factionrep` int(11) not null default '0',
   `questprogress` int(11) not null default '1',
   `questnum` int(11) not null default '0',
   `company` int(11) not null default '0',
   `job` int(11) not null default '0',
   `faction` int(11) not null default '0',
   `factionquest` int(11) not null default '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=82;


CREATE TABLE `companies` (
   `id` int(11) not null auto_increment,
   `ownerid` int(11) not null,
   `brand` varchar(255) not null,
   `hired` int(11) not null default '0',
   `funds` int(11) not null default '1000',
   `profit` int(11) not null default '0',
   `product` int(11) not null default '0',
   `mprice` int(11) not null default '0',
   `quantity` int(11) not null default '0',
   `pay` int(11) not null default '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`ownerid`,`brand`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=47;


CREATE TABLE `entrylog` (
   `id` int(11) not null auto_increment,
   `author` int(11) not null,
   `title` varchar(255) not null,
   `type` int(11) default '3',
   `content` text,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=8;


CREATE TABLE `factions` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) not null,
   `imageid` int(11) not null,
   `funds` int(11) not null default '1000',
   `influence` int(11) not null default '1000',
   `army` int(11) not null default '0',
   `legacy` text,
   `typicalwarrior` int(11) not null,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5;


CREATE TABLE `imagebank` (
   `id` int(11) not null auto_increment,
   `whatis` varchar(255) not null,
   `location` varchar(255) not null,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`location`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=44;


CREATE TABLE `invents` (
   `id` int(11) not null auto_increment,
   `ownerid` int(11) not null,
   `item1` int(11) not null default '0',
   `item2` int(11) not null default '0',
   `item3` int(11) not null default '0',
   `item4` int(11) not null default '0',
   `item5` int(11) not null default '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`ownerid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=65;


CREATE TABLE `items` (
   `id` int(11) not null auto_increment,
   `makeable` int(11) not null default '1',
   `mtype` int(11) not null,
   `mclass` int(11) not null,
   `name` varchar(255) not null,
   `effect` int(11) not null default '0',
   `effect2` int(11) not null default '0',
   `license` int(11) not null,
   `faction` int(11) default '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`name`),
   KEY `mclass` (`mclass`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=33;


CREATE TABLE `monsters` (
   `id` int(11) not null auto_increment,
   `mclass` int(11) not null default '1',
   `level` int(11) not null default '100',
   `name` varchar(255) not null,
   `image` varchar(255) not null,
   `health` int(11) not null default '100',
   `damage` int(11) not null default '0',
   `defense` int(11) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=55;


CREATE TABLE `quests` (
   `id` int(11) not null auto_increment,
   `type` int(11) not null default '1',
   `title` varchar(255),
   `image` int(11),
   `descript` text not null,
   `partysize` int(11) not null default '1',
   `length` int(11) default '1',
   `minion` int(11),
   `boss` int(11) not null default '0',
   `prize` int(11) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=10;


CREATE TABLE `sessions` (
   `id` int(11) not null auto_increment,
   `ownerid` int(11) not null,
   `mykey` varchar(255) not null,
   `logtime` varchar(255),
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=108;


CREATE TABLE `users` (
   `id` int(11) not null auto_increment,
   `creation` varchar(255) not null,
   `username` varchar(255) not null,
   `email` varchar(255) not null,
   `password` varchar(255) not null,
   `ipadd` varchar(255) not null,
   `rights` int(11) not null default '1',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`username`),
   UNIQUE KEY (`email`),
   UNIQUE KEY (`ipadd`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=61;


CREATE TABLE `wars` (
   `id` int(11) not null auto_increment,
   `attacker` int(11) not null,
   `defender` int(11) not null,
   `casualties` int(11) not null default '0',
   `winner` int(11) not null,
   `active` int(11) not null default '1',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=82;
