<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintSSOUrl($url) {
    PrintHTMLIndexHeader();
    printf("<div class=\"jumbotron\">");
    printf("<p>");
    printf("Click the button below to login with your EVE Online account.<br>");
    printf("<a href=\"" . $url . "\">");
    printf("<img src=\"https://images.contentful.com/idjq7aai9ylm/18BxKSXCymyqY4QKo8KwKe/c2bdded6118472dd587c8107f24104d7/EVE_SSO_Login_Buttons_Small_White.png?w=195&h=30\" />");
    printf("</a>");
    printf("</p>");
    printf("</div>");
    PrintHTMLIndexFooter();
}