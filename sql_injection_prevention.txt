// ============================================================
// Online Payment System - SQL Injection Prevention
// Pseudocode
// ============================================================

FUNCTION processPayment(request)
    // Step 1: Validate all input on the server (never trust the client)
    cardholderName = request.get("cardholderName")
    amount         = request.get("amount")
    orderId        = request.get("orderId")

    IF NOT matchesPattern(cardholderName, "^[A-Za-z .'-]{1,50}$") THEN
        RETURN errorResponse("Invalid payment details")
    IF NOT isPositiveDecimal(amount) OR amount > MAX_TRANSACTION_LIMIT THEN
        RETURN errorResponse("Invalid payment details")
    IF NOT isInteger(orderId) THEN
        RETURN errorResponse("Invalid payment details")

    // Step 2: Use a parameterized query (input is never concatenated into SQL)
    query = "INSERT INTO payments (order_id, cardholder_name, amount, created_at)
             VALUES (?, ?, ?, NOW())"

    TRY
        // Step 3: Connect with a least-privilege database account
        connection = database.connect(PAYMENT_APP_USER)   // INSERT/SELECT only
        statement  = connection.prepare(query)
        statement.bind(1, orderId)
        statement.bind(2, cardholderName)
        statement.bind(3, amount)
        statement.execute()
    CATCH DatabaseError AS e
        // Step 4: Log details internally, show a generic message to the user
        securityLog.write(e.details, request.ipAddress)
        RETURN errorResponse("Payment could not be processed")
    END TRY

    RETURN successResponse("Payment received")
END FUNCTION
