<?php

function encrypt_value($value){

	return CI_INSTANCE()->encrypter->encrypt($value);
}

function decrypt_value($value){

	return CI_INSTANCE()->encrypter->decrypt($value);
}
