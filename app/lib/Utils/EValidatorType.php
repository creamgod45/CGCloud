<?php

namespace App\Lib\Utils;

enum EValidatorType
{
    case LOGIN;
    case REGISTER;
    case FORGOTPASSWORD;
    case RESETPASSWORD;
    case RESETPASSWORDPOST;
    case ANIMALCREATE;
    case PROFILEGENERAL;
    case PROFILEUPDATEEMAIL;
    case PROFILEUPDATEPASSWORD;
    case SENDMAILVERIFYCODE;
    case EMAILVERIFICATION;
    case VERIFYCODE;
    case NEWMAILVERIFYCODE;
    case NULL;
    case Language;
    case GETCLIENTID;
    case SEARCH;
    case IMAGESGEN;
    case SYSTEMSETTINGUPLOAD;
    case SHARETABLECREATE;
    case SHARETABLEEDIT;
}
