// ============================================================
// Online Payment System - Cross-Site Scripting (XSS) Prevention
// Pseudocode
// ============================================================

FUNCTION displayPaymentReceipt(paymentId, session)
    // Step 1: Confirm the user is authorized to view this payment
    payment = database.getPaymentById(paymentId)   // parameterized query
    IF payment.ownerId != session.userId THEN
        RETURN errorResponse("Access denied")

    // Step 2: Encode all user-supplied data before placing it in HTML
    safeName    = htmlEncode(payment.cardholderName)
    safeAddress = htmlEncode(payment.billingAddress)
    maskedCard  = "**** **** **** " + payment.cardLastFour

    // Step 3: Set security headers on the response
    response.setHeader("Content-Security-Policy",
                       "default-src 'self'; script-src 'self'")
    response.setHeader("X-Content-Type-Options", "nosniff")

    // Step 4: Protect the session cookie from script access
    response.setCookie("sessionId", session.id,
                       HttpOnly = TRUE, Secure = TRUE, SameSite = "Strict")

    RETURN renderPage("receipt.html", safeName, safeAddress, maskedCard)
END FUNCTION


FUNCTION htmlEncode(text)
    // Replace characters that have special meaning in HTML
    text = replace(text, "&", "&amp;")
    text = replace(text, "<", "&lt;")
    text = replace(text, ">", "&gt;")
    text = replace(text, "\"", "&quot;")
    text = replace(text, "'", "&#x27;")
    RETURN text
END FUNCTION
