# Securing an Online Payment System Against SQL Injection and Cross-Site Scripting

## Introduction

Online payment systems handle some of the most sensitive data an organization stores, including names, billing addresses, and card details. Two of the most common ways attackers target these systems are SQL injection and cross-site scripting (XSS). This report explains the pseudocode in the [`pseudocode`](pseudocode/) folder and how each part defends against these attacks. The design follows a defense-in-depth approach, meaning several independent layers of protection work together so that the failure of one layer does not expose the system.

## SQL Injection

### The Threat

SQL injection occurs when an attacker submits input that the database interprets as part of a SQL command rather than as data. For example, entering `' OR '1'='1` in a form field could bypass a check, and entering `'; DROP TABLE payments; --` could destroy stored records. Because many applications build queries from user input, any field that reaches the database is a possible entry point, and a successful attack can expose or export an entire database (Richardson & Thies, 2012).

### Defenses in the Pseudocode

The `processPayment` function in [`sql_injection_prevention.txt`](pseudocode/sql_injection_prevention.txt) uses four layers of protection.

**Server-side input validation.** Each field is checked against a strict pattern before it is used. The cardholder name may contain only letters and a few punctuation marks, the amount must be a positive number under a set limit, and the order ID must be an integer. Anything that does not match is rejected. This check runs on the server because an attacker can submit a forged form directly and skip any validation performed in the browser (Richardson & Thies, 2012).

**Parameterized queries.** This is the primary defense. Instead of combining user input with SQL text, the query uses `?` placeholders, and the values are bound separately. The database treats the bound values strictly as data, so even a malicious string is stored as ordinary text and never executed.

**Least-privilege database account.** The application connects with an account that can only insert and read payment records. If an attack somehow succeeded, the account would not have permission to delete tables or reach unrelated data, which limits the damage.

**Generic error messages.** Database errors are written to an internal security log, while the user sees only a short, general message. Detailed errors can reveal table names or query structure, which helps an attacker refine an attack (Richardson & Thies, 2012).

## Cross-Site Scripting

### The Threat

Cross-site scripting occurs when an attacker manages to place malicious JavaScript in a page that other users will load. For instance, an attacker might enter `<script>stealCookies()</script>` as a billing name. If the application displays that name without processing it, the browser runs the script. On a payment site, this could allow the attacker to steal session cookies, hijack accounts, or capture card details as they are typed.

### Defenses in the Pseudocode

The `displayPaymentReceipt` and `htmlEncode` functions in [`xss_prevention.txt`](pseudocode/xss_prevention.txt) also use four layers of protection.

**Authorization check.** Before showing a receipt, the system confirms that the payment belongs to the logged-in user. This stops an attacker from viewing another customer's payment by changing the ID in the URL.

**Output encoding.** This is the primary defense. The `htmlEncode` function converts characters such as `<`, `>`, and quotation marks into safe HTML entities, so a script tag appears on the page as plain text instead of running. Data must be cleaned for the language in which it is finally used, because input that is harmless in one part of a system can become dangerous when it reaches another component written in a different language (Richardson & Thies, 2012). In this case, that destination is HTML. The card number is also masked so the full number is never displayed.

**Content Security Policy.** The response includes a header instructing the browser to run scripts only from the site's own domain. Even if a malicious script were injected, the browser would refuse to execute it.

**Secure session cookies.** The session cookie is marked `HttpOnly`, which prevents JavaScript from reading it, so a script cannot steal it. The `Secure` flag ensures the cookie is sent only over HTTPS, and `SameSite=Strict` helps block cross-site request forgery.

## Conclusion

No single control makes a payment system safe. Input validation reduces the amount of harmful data that enters the system, while parameterized queries and output encoding ensure that any harmful data that does get through cannot be executed. Least privilege, quiet error messages, and secure cookies further limit what an attacker can learn or reach. Because a payment system protects customers' financial information, these defenses should aim to prevent attacks outright rather than simply recover from them, which is the strongest level of system hardening described by Richardson and Thies (2012).

## Reference

Richardson, T., & Thies, C. N. (2012). *Secure software design*. Jones & Bartlett Learning.
