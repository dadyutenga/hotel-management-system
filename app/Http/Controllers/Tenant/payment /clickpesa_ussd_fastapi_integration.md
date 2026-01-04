# 💸 ClickPesa Mobile Money Integration (USSD Push Payments)

## 🔑 Overview

This guide documents how to integrate **ClickPesa Mobile Money USSD Push Payments** into your **Django backend**.

You'll learn how to:

- Generate an authorization token  
- Preview a USSD push transaction  
- Initiate a USSD push payment  
- Query the transaction status  

> ⚠️ All requests must use HTTPS and include your `client-id`, `api-key`, and the `Bearer` token. Mobile Money Integration (USSD Push Payments)

## 🔑 Overview

This guide documents how to integrate **ClickPesa Mobile Money USSD Push Payments** into your **FastAPI backend**.

You’ll learn how to:

- Generate an authorization token  
- Preview a USSD push transaction  
- Initiate a USSD push payment  
- Query the transaction status  

> ⚠️ All requests must use HTTPS and include your `client-id`, `api-key`, and the `Bearer` token.

---

## 🧠 Base URL

```
https://api.clickpesa.com
```

---

## 🔐 1. Generate Token

Used to authenticate all API calls.

**Endpoint:**
```
POST /third-parties/generate-token
```

**Headers:**
| Key | Value |
|-----|--------|
| Content-Type | application/json |
| client-id | YOUR_CLIENT_ID |
| api-key | YOUR_API_KEY |

**Response:**
```json
{
  "success": true,
  "token": "Bearer eyJhbGciOiJIUzI1..."
}
```

> The token is valid for **1 hour** — regenerate it when expired.

---

## 📲 2. Preview USSD Push Payment

Used to validate a transaction before triggering payment.

**Endpoint:**
```
POST /third-parties/payments/preview-ussd-push
```

**Headers:**
| Key | Value |
|-----|--------|
| Content-Type | application/json |
| Authorization | Bearer YOUR_JWT_TOKEN |

**Body Example:**
```json
{
  "reference": "ORD-2025-001",
  "amount": 10000,
  "currency": "TZS",
  "msisdn": "255714123456",
  "provider": "AIRTEL"
}
```

**Response Example:**
```json
{
  "success": true,
  "message": "Payment preview successful",
  "data": {
    "amount": 10000,
    "currency": "TZS",
    "msisdn": "255714123456",
    "provider": "AIRTEL"
  }
}
```

---

## 🚀 3. Initiate USSD Push Payment

This triggers the actual USSD push on the customer’s phone.

**Endpoint:**
```
POST /third-parties/payments/initiate-ussd-push
```

**Headers:**
| Key | Value |
|-----|--------|
| Content-Type | application/json |
| Authorization | Bearer YOUR_JWT_TOKEN |

**Body Example:**
```json
{
  "reference": "ORD-2025-001",
  "amount": 10000,
  "currency": "TZS",
  "msisdn": "255714123456",
  "provider": "AIRTEL",
  "callback_url": "https://yourdomain.com/payment/callback"
}
```

**Response Example:**
```json
{
  "success": true,
  "message": "USSD push initiated",
  "data": {
    "transaction_id": "TXN_987654321",
    "status": "PENDING"
  }
}
```

---

## 🔍 4. Query Payment Status

To verify if the customer completed the payment.

**Endpoint:**
```
GET /third-parties/payments/query-payment-status
```

**Headers:**
| Key | Value |
|-----|--------|
| Content-Type | application/json |
| Authorization | Bearer YOUR_JWT_TOKEN |

**Query Parameters:**
| Param | Description |
|--------|--------------|
| transaction_id | The transaction ID returned after initiation |

**Example:**
```
GET /third-parties/payments/query-payment-status?transaction_id=TXN_987654321
```

**Response Example:**
```json
{
  "success": true,
  "data": {
    "transaction_id": "TXN_987654321",
    "status": "SUCCESS",
    "amount": 10000,
    "currency": "TZS",
    "msisdn": "255714123456",
    "provider": "AIRTEL"
  }
}
```

> Statuses may include: `PENDING`, `SUCCESS`, `FAILED`.

---

## ⚙️ 5. Sample Django Integration

```python
import requests
from django.conf import settings

BASE_URL = "https://api.clickpesa.com"
CLIENT_ID = settings.CLICKPESA_CLIENT_ID
API_KEY = settings.CLICKPESA_API_KEY

def generate_token():
    response = requests.post(
        f"{BASE_URL}/third-parties/generate-token",
        headers={
            "client-id": CLIENT_ID,
            "api-key": API_KEY,
            "Content-Type": "application/json",
        }
    )
    return response.json()["token"]

def initiate_payment(msisdn, amount, reference):
    token = generate_token()
    payload = {
        "reference": reference,
        "amount": amount,
        "currency": "TZS",
        "msisdn": msisdn,
        "provider": "AIRTEL",
        "callback_url": "https://yourdomain.com/payment/callback/"
    }
    response = requests.post(
        f"{BASE_URL}/third-parties/payments/initiate-ussd-push",
        headers={
            "Authorization": token,
            "Content-Type": "application/json",
        },
        json=payload
    )
    return response.json()
```

---

## 🧾 Webhook Callback Example

ClickPesa will send a POST request to your `callback_url` after transaction completion.

**Example Payload:**
```json
{
  "transaction_id": "TXN_987654321",
  "reference": "ORD-2025-001",
  "status": "SUCCESS",
  "amount": 10000,
  "currency": "TZS",
  "msisdn": "255714123456"
}
```

Make sure your Django view:
- Accepts `POST` requests  
- Returns `200 OK` quickly  
- Verifies the payload integrity
- Uses CSRF exemption for webhook endpoints

---

## ✅ Summary

| Action | Endpoint | Method |
|---------|-----------|--------|
| Generate Token | `/third-parties/generate-token` | POST |
| Preview Payment | `/third-parties/payments/preview-ussd-push` | POST |
| Initiate Payment | `/third-parties/payments/initiate-ussd-push` | POST |
| Query Status | `/third-parties/payments/query-payment-status` | GET |

---

🧠 **Pro Tips**
- Always store `transaction_id` for later verification.
- Handle expired tokens automatically.
- Use HTTPS for all webhooks and API calls.
- Validate MSISDN numbers (E.164 format).
- Add CLICKPESA_CLIENT_ID and CLICKPESA_API_KEY to your Django settings.
- Use Django's CSRF exemption decorator for webhook endpoints.
