# OWASP ZAP - AsiloSoft (ambiente de teste local)

ZAP by [Checkmarx](https://checkmarx.com/).


## Summary of Alerts

| Risk Level | Number of Alerts |
| --- | --- |
| High | 0 |
| Medium | 0 |
| Low | 0 |
| Informational | 3 |




## Insights

| Level | Reason | Site | Description | Statistic |
| --- | --- | --- | --- | --- |
| Low | Warning |  | ZAP errors logged - see the zap.log file for details | 2    |
| Low | Warning |  | ZAP warnings logged - see the zap.log file for details | 18    |
| Info | Informational | http://127.0.0.1:18092 | Percentage of responses with status code 2xx | 72 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of responses with status code 3xx | 25 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of responses with status code 4xx | 1 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with content type application/javascript | 4 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with content type text/css | 4 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with content type text/html | 83 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with content type text/plain | 8 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with method GET | 37 % |
| Info | Informational | http://127.0.0.1:18092 | Percentage of endpoints with method POST | 62 % |
| Info | Informational | http://127.0.0.1:18092 | Count of total endpoints | 24    |




## Alerts

| Name | Risk Level | Number of Instances |
| --- | --- | --- |
| Session Management Response Identified | Informational | 2 |
| User Agent Fuzzer | Informational | Systemic |
| User Controllable HTML Element Attribute (Potential XSS) | Informational | 2 |




## Alert Detail



### [ Session Management Response Identified ](https://www.zaproxy.org/docs/alerts/10112/)



##### Informational (Medium)

### Description

The given response has been identified as containing a session management token. The 'Other Info' field contains a set of header tokens that can be used in the Header Based Session Management Method. If the request is in a context which has a Session Management Method set to "Auto-Detect" then this rule will change the session management to use the tokens identified.

* URL: http://127.0.0.1:18092/%3Froute=sair
  * Node Name: `http://127.0.0.1:18092/ (route)(csrf_token)`
  * Method: `POST`
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


Instances: 2

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
* URL: http://127.0.0.1:18092/%3Froute=compras-nova
  * Node Name: `http://127.0.0.1:18092/ (route)`
  * Method: `GET`
  * Parameter: `Header User-Agent`
  * Attack: `Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)`
  * Evidence: ``
  * Other Info: ``
* URL: http://127.0.0.1:18092/%3Froute=usuarios&situacao=ativos
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

* URL: http://127.0.0.1:18092/%3Fid=1&route=quartos-editar
  * Node Name: `http://127.0.0.1:18092/ (id,route)`
  * Method: `GET`
  * Parameter: `id`
  * Attack: ``
  * Evidence: ``
  * Other Info: `User-controlled HTML attribute values were found. Try injecting special characters to see if XSS might be possible. The page at the following URL:

http://127.0.0.1:18092/?id=1&route=quartos-editar

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


Instances: 2

### Solution

Validate all input and sanitize output it before writing to any HTML attributes.

### Reference


* [ https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html ](https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html)


#### CWE Id: [ 20 ](https://cwe.mitre.org/data/definitions/20.html)


#### WASC Id: 20

#### Source ID: 3


