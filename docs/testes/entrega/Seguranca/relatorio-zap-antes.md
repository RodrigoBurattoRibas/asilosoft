# OWASP ZAP - AsiloSoft (ambiente de teste local)

ZAP by [Checkmarx](https://checkmarx.com/).


## Summary of Alerts

| Risk Level | Number of Alerts |
| --- | --- |
| High | 0 |
| Medium | 0 |
| Low | 2 |
| Informational | 3 |




## Insights

| Level | Reason | Site | Description | Statistic |
| --- | --- | --- | --- | --- |
| Low | Warning |  | ZAP errors logged - see the zap.log file for details | 2    |
| Low | Warning |  | ZAP warnings logged - see the zap.log file for details | 18    |
| Info | Informational | http://127.0.0.1:18092 | Percentage of responses with status code 2xx | 65 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of responses with status code 3xx | 32 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of responses with status code 4xx | 2 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with content type application/javascript | 5 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with content type text/css | 5 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with content type text/html | 77 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with content type text/plain | 11 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with method GET | 50 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with method POST | 50 % |
| Info | Informational | http://127.0.0.1:18092 | Count of total endpoints | 18    |




## Alerts

| Name | Risk Level | Number of Instances |
| --- | --- | --- |
| Server Leaks Information via "X-Powered-By" HTTP Response Header Field(s) | Low | 1 |
| X-Content-Type-Options Header Missing | Low | 2 |
| Session Management Response Identified | Informational | 3 |
| User Agent Fuzzer | Informational | Systemic |
| User Controllable HTML Element Attribute (Potential XSS) | Informational | 4 |




## Alert Detail



### [ Server Leaks Information via "X-Powered-By" HTTP Response Header Field(s) ](https://www.zaproxy.org/docs/alerts/10037/)



##### Low (Medium)

### Description

The web/application server is leaking information via one or more "X-Powered-By" HTTP response headers. Access to such information may facilitate attackers identifying other frameworks/components your web application is reliant upon and the vulnerabilities such components may be subject to.

* URL: http://127.0.0.1:18092/robots.txt
  * Node Name: `http://127.0.0.1:18092/robots.txt`
  * Method: `GET`
  * Parameter: ``
  * Attack: ``
  * Evidence: `X-Powered-By: PHP/8.5.4`
  * Other Info: ``


Instances: 1

### Solution

Ensure that your web server, application server, load balancer, etc. is configured to suppress "X-Powered-By" headers.

### Reference


* [ https://owasp.org/www-project-web-security-testing-guide/v42/4-Web_Application_Security_Testing/01-Information_Gathering/08-Fingerprint_Web_Application_Framework ](https://owasp.org/www-project-web-security-testing-guide/v42/4-Web_Application_Security_Testing/01-Information_Gathering/08-Fingerprint_Web_Application_Framework)
* [ https://www.troyhunt.com/shhh-dont-let-your-response-headers/ ](https://www.troyhunt.com/shhh-dont-let-your-response-headers/)


#### CWE Id: [ 497 ](https://cwe.mitre.org/data/definitions/497.html)


#### WASC Id: 13

#### Source ID: 3

### [ X-Content-Type-Options Header Missing ](https://www.zaproxy.org/docs/alerts/10021/)



##### Low (Medium)

### Description

The Anti-MIME-Sniffing header X-Content-Type-Options was not set to 'nosniff'. This allows older versions of Internet Explorer and Chrome to perform MIME-sniffing on the response body, potentially causing the response body to be interpreted and displayed as a content type other than the declared content type. Current (early 2014) and legacy versions of Firefox will use the declared content type (if one is set), rather than performing MIME-sniffing.

* URL: http://127.0.0.1:18092/src/assets/css/asilosoft.css
  * Node Name: `http://127.0.0.1:18092/src/assets/css/asilosoft.css`
  * Method: `GET`
  * Parameter: `x-content-type-options`
  * Attack: ``
  * Evidence: ``
  * Other Info: `This issue still applies to error type pages (401, 403, 500, etc.) as those pages are often still affected by injection issues, in which case there is still concern for browsers sniffing pages away from their actual content type.
At "High" threshold this scan rule will not alert on client or server error responses.`
* URL: http://127.0.0.1:18092/src/assets/js/asilosoft.js
  * Node Name: `http://127.0.0.1:18092/src/assets/js/asilosoft.js`
  * Method: `GET`
  * Parameter: `x-content-type-options`
  * Attack: ``
  * Evidence: ``
  * Other Info: `This issue still applies to error type pages (401, 403, 500, etc.) as those pages are often still affected by injection issues, in which case there is still concern for browsers sniffing pages away from their actual content type.
At "High" threshold this scan rule will not alert on client or server error responses.`


Instances: 2

### Solution

Ensure that the application/web server sets the Content-Type header appropriately, and that it sets the X-Content-Type-Options header to 'nosniff' for all web pages.
If possible, ensure that the end user uses a standards-compliant and modern web browser that does not perform MIME-sniffing at all, or that can be directed by the web application/web server to not perform MIME-sniffing.

### Reference


* [ https://learn.microsoft.com/en-us/previous-versions/windows/internet-explorer/ie-developer/compatibility/gg622941(v=vs.85) ](https://learn.microsoft.com/en-us/previous-versions/windows/internet-explorer/ie-developer/compatibility/gg622941(v=vs.85))
* [ https://owasp.org/www-community/Security_Headers ](https://owasp.org/www-community/Security_Headers)


#### CWE Id: [ 693 ](https://cwe.mitre.org/data/definitions/693.html)


#### WASC Id: 15

#### Source ID: 3

### [ Session Management Response Identified ](https://www.zaproxy.org/docs/alerts/10112/)



##### Informational (Medium)

### Description

The given response has been identified as containing a session management token. The 'Other Info' field contains a set of header tokens that can be used in the Header Based Session Management Method. If the request is in a context which has a Session Management Method set to "Auto-Detect" then this rule will change the session management to use the tokens identified.

* URL: http://127.0.0.1:18092/%3Fid=8&route=usuarios-editar
  * Node Name: `http://127.0.0.1:18092/ (id,route)`
  * Method: `GET`
  * Parameter: `PHPSESSID`
  * Attack: ``
  * Evidence: `PHPSESSID`
  * Other Info: `cookie:PHPSESSID`
* URL: http://127.0.0.1:18092/%3Froute=sair
  * Node Name: `http://127.0.0.1:18092/ (route)(csrf_token)`
  * Method: `POST`
  * Parameter: `PHPSESSID`
  * Attack: ``
  * Evidence: `PHPSESSID`
  * Other Info: `cookie:PHPSESSID`
* URL: http://127.0.0.1:18092/%3Fid=1&route=idosos-saude
  * Node Name: `http://127.0.0.1:18092/ (id,route)`
  * Method: `GET`
  * Parameter: `PHPSESSID`
  * Attack: ``
  * Evidence: `PHPSESSID`
  * Other Info: `cookie:PHPSESSID`


Instances: 3

### Solution

This is an informational alert rather than a vulnerability and so there is nothing to fix.

### Reference


* [ https://www.zaproxy.org/docs/desktop/addons/authentication-helper/session-mgmt-id/ ](https://www.zaproxy.org/docs/desktop/addons/authentication-helper/session-mgmt-id/)



#### Source ID: 3

### [ User Agent Fuzzer ](https://www.zaproxy.org/docs/alerts/10104/)



##### Informational (Medium)

### Description

Check for differences in response based on fuzzed User Agent (eg. mobile sites, access as a Search Engine Crawler). Compares the response statuscode and the hashcode of the response body with the original response.

* URL: http://127.0.0.1:18092/%3Fbusca=ZAP&route=idosos&situacao=ativos
  * Node Name: `http://127.0.0.1:18092/ (busca,route,situacao)`
  * Method: `GET`
  * Parameter: `Header User-Agent`
  * Attack: `Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)`
  * Evidence: ``
  * Other Info: ``
* URL: http://127.0.0.1:18092/%3Froute=quartos-novo
  * Node Name: `http://127.0.0.1:18092/ (route)`
  * Method: `GET`
  * Parameter: `Header User-Agent`
  * Attack: `Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)`
  * Evidence: ``
  * Other Info: ``
* URL: http://127.0.0.1:18092/%3Froute=idosos&situacao=ativos
  * Node Name: `http://127.0.0.1:18092/ (route,situacao)`
  * Method: `GET`
  * Parameter: `Header User-Agent`
  * Attack: `Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)`
  * Evidence: ``
  * Other Info: ``

Instances: Systemic


### Solution



### Reference


* [ https://owasp.org/wstg ](https://owasp.org/wstg)



#### Source ID: 1

### [ User Controllable HTML Element Attribute (Potential XSS) ](https://www.zaproxy.org/docs/alerts/10031/)



##### Informational (Low)

### Description

This check looks at user-supplied input in query string parameters and POST data to identify where certain HTML attribute values might be controlled. This provides hot-spot detection for XSS (cross-site scripting) that will require further review by a security analyst to determine exploitability.

* URL: http://127.0.0.1:18092/%3Fid=1&route=usuarios-editar
  * Node Name: `http://127.0.0.1:18092/ (id,route)`
  * Method: `GET`
  * Parameter: `id`
  * Attack: ``
  * Evidence: ``
  * Other Info: `User-controlled HTML attribute values were found. Try injecting special characters to see if XSS might be possible. The page at the following URL:

http://127.0.0.1:18092/?id=1&route=usuarios-editar

appears to include user input in:
a(n) [meta] tag [content] attribute

The user input found was:
id=1

The user-controlled value was:
width=device-width, initial-scale=1`
* URL: http://127.0.0.1:18092/%3Froute=idosos
  * Node Name: `http://127.0.0.1:18092/ (route)`
  * Method: `GET`
  * Parameter: `route`
  * Attack: ``
  * Evidence: ``
  * Other Info: `User-controlled HTML attribute values were found. Try injecting special characters to see if XSS might be possible. The page at the following URL:

http://127.0.0.1:18092/?route=idosos

appears to include user input in:
a(n) [input] tag [value] attribute

The user input found was:
route=idosos

The user-controlled value was:
idosos`
* URL: http://127.0.0.1:18092/%3Froute=idosos&situacao=desativados
  * Node Name: `http://127.0.0.1:18092/ (route,situacao)`
  * Method: `GET`
  * Parameter: `route`
  * Attack: ``
  * Evidence: ``
  * Other Info: `User-controlled HTML attribute values were found. Try injecting special characters to see if XSS might be possible. The page at the following URL:

http://127.0.0.1:18092/?route=idosos&situacao=desativados

appears to include user input in:
a(n) [input] tag [value] attribute

The user input found was:
route=idosos

The user-controlled value was:
idosos`
* URL: http://127.0.0.1:18092/%3Froute=idosos&situacao=desativados
  * Node Name: `http://127.0.0.1:18092/ (route,situacao)`
  * Method: `GET`
  * Parameter: `situacao`
  * Attack: ``
  * Evidence: ``
  * Other Info: `User-controlled HTML attribute values were found. Try injecting special characters to see if XSS might be possible. The page at the following URL:

http://127.0.0.1:18092/?route=idosos&situacao=desativados

appears to include user input in:
a(n) [input] tag [value] attribute

The user input found was:
situacao=desativados

The user-controlled value was:
desativados`


Instances: 4

### Solution

Validate all input and sanitize output it before writing to any HTML attributes.

### Reference


* [ https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html ](https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html)


#### CWE Id: [ 20 ](https://cwe.mitre.org/data/definitions/20.html)


#### WASC Id: 20

#### Source ID: 3


