// Encrypted transport via TLS 

SSLContext sslContext = SSLContext.getInstance("TLS"); 
sslContext.init(keyManagerFactory.getKeyManagers(), null, null); 
SSLServerSocketFactory sslServerSocketFactory = sslContext.getServerSocketFactory(); 

// Both files use SSLSocket/SSLServerSocket rather than plain Socket. This means all data exchanged between client and server is encrypted in transit. This directly protects against the eavesdropping risk 

//Server authentication via keystore 

KeyStore keyStore = KeyStore.getInstance("JKS"); 
keyStore.load(new FileInputStream(keystoreFile), keystorePassword.toCharArray()); 
KeyManagerFactory keyManagerFactory = KeyManagerFactory.getInstance("SunX509"); 
keyManagerFactory.init(keyStore, keystorePassword.toCharArray()); 

//The server loads its private key and certificate from a keystore and presents that certificate during the TLS handshake. This lets the client cryptographically verify it's talking to the real server rather than an impostor 

//Client-side certificate validation via truststore 

KeyStore trustStore = KeyStore.getInstance("JKS"); 
trustStore.load(new FileInputStream(truststoreFile), truststorePassword.toCharArray()); 
TrustManagerFactory trustManagerFactory = TrustManagerFactory.getInstance("SunX509"); 
trustManagerFactory.init(trustStore); 

//The client only trusts certificates signed by authorities present in its truststore, rather than blindly accepting any certificate. This is the client-side counterpart to server authentication, and it's what actually enforces the trust relationship instead of just encrypting traffic between two unverified parties. 

//Standard, non-custom cryptography 

//The code relies on Java's built-in SSLContext, KeyManagerFactory, and TrustManagerFactory rather than a homegrown encryption scheme. 

//Closed open ports 
