<?php

namespace App;

class Config{
	const DB_HOST='localhost';
	const DB_NAME='mvclogin';
	const DB_USER='pz';
	const DB_PASS='pz';
	const SHOW_ERRORS=true;//false for production, errors saved to file in logs catalogue;
	const SECRET_KEY='oFP0Fn4P5hdm8oKBtBpugCnbHdgYyKFT'; 
	const MAILGUN_API_KEY = 'c75e879027f9a8f5c7c3147432b3e970-c50f4a19-07f6246c';
	const MAILGUN_DOMAIN='sandbox7245ad6fc7724ecda49aa89dcb4c50b4.mailgun.org';
	//key generator https://randomkeygen.com/;
}