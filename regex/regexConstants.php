<?php

abstract class regexConstants
{
    const twitter_regex = "/(https:\/\/twitter.com\/(?![a-zA-Z0-9_]+\/)([a-zA-Z0-9_]+))/";
    const instagram_regex = "/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_]+)/im";
    const linkedin_regex = "/^https:\/\/[a-z]{2,3}\.linkedin\.com\/.*$/";
    const website_regex = "/(http:\/\/|https:\/\/)?(www.)?([a-zA-Z0-9]+).[a-zA-Z0-9]*.[a-z]{3}.?([a-z]+)?/";
    const facebook_regex = "/^(https?:\/\/)?((w{3}\.)?)facebook.com\/.*/i";
}