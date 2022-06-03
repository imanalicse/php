<?php

abstract class regexConstants
{
    const website_regex = "/(http:\/\/|https:\/\/)?(www.)?([a-zA-Z0-9]+).[a-zA-Z0-9]*.[a-z]{3}.?([a-z]+)?/";
    const twitter_regex = "/(?:http:\/\/)?(?:www\.)?twitter\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*([\w\-]*)/";
    const instagram_regex = "/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_]+)/im";
    const linkedin_regex = "/(?:http:\/\/)?(?:www\.)?linkedin\.com\/.*$/";
    const facebook_regex = "/^(https?:\/\/)?((w{3}\.)?)facebook.com\/.*/i";
    const all_alphabet_regex = "/^[A-Za-z]+$/";
    const all_number_regex = "/^[0-9]+$/";
    const only_six_number_regex = "/[0-9]{6}/";

    //Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character:
    const password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$#!%*?&])[A-Za-z\d@#$!%*?&]{8,}$/";
}