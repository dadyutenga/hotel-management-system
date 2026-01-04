# Payment Confirmation Guide

## Overview
This guide explains how to confirm payment status after initiating a USSD push payment with ClickPesa.

## Payment Flow

### 1. Initiate Payment
```http
POST /api/v1/payments/
Authorization: Bearer <your_token>
Content-Type: application/json

{
  "amount": 1000,
  "currency": "TZS",
  "msisdn": "255680185784",
  "provider": "AIRTEL",
  "customer_name": "Test User"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Payment initiated successfully. Customer will receive USSD push prompt.",
  "data": {
    "id": "4d6aafd6-f2c0-49e4-bb2f-aefd5bc5e5ef",
    "transaction_id": "LCPCANS7MP",
    "reference": "PAY3A44E1A7FC81",
    "status": "processing",
    "amount": "1000.00",
    "currency": "TZS",
    "msisdn": "255680185784",
    "provider": "AIRTEL",
    ...
  }
}
```

**Important Fields:**
- `id` - Your payment UUID (use this to check status)
- `transaction_id` - ClickPesa transaction ID (e.g., "LCPCANS7MP")
- `reference` - Order reference (e.g., "PAY3A44E1A7FC81")
- `status` - Initial status will be "processing"

---

### 2. Check Payment Status (After 15 seconds)

**Wait 15-30 seconds** after initiating payment before checking status. This gives the customer time to approve the USSD prompt.

```http
GET /api/v1/payments/{payment_id}/status/
Authorization: Bearer <your_token>
```

**Example:**
```http
GET /api/v1/payments/4d6aafd6-f2c0-49e4-bb2f-aefd5bc5e5ef/status/
Authorization: Bearer <your_token>
```

**Success Response:**
```json
{
  "success": true,
  "message": "Payment status retrieved successfully",
  "data": {
    "id": "4d6aafd6-f2c0-49e4-bb2f-aefd5bc5e5ef",
    "transaction_id": "LCPCANS7MP",
    "reference": "PAY3A44E1A7FC81",
    "status": "success",
    "amount": "1000.00",
    "currency": "TZS",
    "completed_at": "2025-10-01T11:52:15.000Z",
    ...
  },
  "provider_status": {
    "id": "LCPCANS7MP",
    "status": "SUCCESS",
    "channel": "AIRTEL-MONEY",
    "orderReference": "PAY3A44E1A7FC81",
    "collectedAmount": "1000.00",
    "collectedCurrency": "TZS",
    "createdAt": "2025-10-01T11:51:42.303Z"
  }
}
```

**Processing Response (Customer hasn't approved yet):**
```json
{
  "success": true,
  "message": "Payment status retrieved successfully",
  "data": {
    "status": "processing",
    ...
  },
  "provider_status": {
    "status": "PROCESSING",
    ...
  }
}
```

**Failed Response:**
```json
{
  "success": true,
  "message": "Payment status retrieved successfully",
  "data": {
    "status": "failed",
    "error_message": "Payment rejected by customer",
    ...
  },
  "provider_status": {
    "status": "FAILED",
    ...
  }
}
```

---

## Payment Status Codes

| Status | Description |
|--------|-------------|
| `pending` | Payment created but not yet sent to provider |
| `processing` | USSD push sent, waiting for customer approval |
| `success` | Payment completed successfully |
| `failed` | Payment failed or rejected |
| `cancelled` | Payment cancelled |

---

## ClickPesa Response Fields

### When Payment is Initiated
ClickPesa returns these fields in the response:

```json
{
  "id": "LCPCANS7MP",              // ClickPesa transaction ID (saved as transaction_id)
  "status": "PROCESSING",          // Current status
  "channel": "AIRTEL-MONEY",       // Provider channel
  "orderReference": "PAY3A44E1A7FC81",  // Your order reference
  "collectedAmount": "1000.00",    // Amount collected
  "collectedCurrency": "TZS",      // Currency
  "createdAt": "2025-10-01T11:51:42.303Z",  // Creation timestamp
  "clientId": "ID2tdiKYChpI44gKitdMrrf4QPwdzOpV"  // Your client ID
}
```

### What Gets Saved in Database

| Database Field | Source | Example |
|----------------|--------|---------|
| `id` | Auto-generated UUID | `4d6aafd6-f2c0-49e4-bb2f-aefd5bc5e5ef` |
| `transaction_id` | ClickPesa `id` field | `LCPCANS7MP` |
| `reference` | Auto-generated | `PAY3A44E1A7FC81` |
| `status` | Mapped from ClickPesa `status` | `processing` → `success`/`failed` |
| `clickpesa_response` | Full ClickPesa response | Complete JSON object |

---

## Implementation Examples

### JavaScript/Frontend (Recommended Flow)

```javascript
// Step 1: Initiate payment
async function initiatePayment() {
  const response = await fetch('http://localhost:8000/api/v1/payments/', {
    method: 'POST',
    headers: {
      'Authorization': 'Bearer ' + token,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      amount: 1000,
      currency: 'TZS',
      msisdn: '255680185784',
      provider: 'AIRTEL',
      customer_name: 'Test User'
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    console.log('Payment initiated:', data.data.id);
    console.log('Transaction ID:', data.data.transaction_id);
    
    // Wait 15 seconds then check status
    setTimeout(() => checkPaymentStatus(data.data.id), 15000);
  }
}

// Step 2: Check payment status
async function checkPaymentStatus(paymentId) {
  const response = await fetch(
    `http://localhost:8000/api/v1/payments/${paymentId}/status/`,
    {
      headers: {
        'Authorization': 'Bearer ' + token
      }
    }
  );
  
  const data = await response.json();
  
  if (data.success) {
    console.log('Payment status:', data.data.status);
    
    if (data.data.status === 'processing') {
      // Still processing, check again in 10 seconds
      setTimeout(() => checkPaymentStatus(paymentId), 10000);
    } else if (data.data.status === 'success') {
      console.log('Payment successful!');
      console.log('Transaction ID:', data.data.transaction_id);
    } else if (data.data.status === 'failed') {
      console.log('Payment failed:', data.data.error_message);
    }
  }
}
```

### Python Example

```python
import requests
import time

BASE_URL = 'http://localhost:8000/api/v1'
TOKEN = 'your_bearer_token'

headers = {
    'Authorization': f'Bearer {TOKEN}',
    'Content-Type': 'application/json'
}

# Step 1: Initiate payment
def initiate_payment():
    response = requests.post(
        f'{BASE_URL}/payments/',
        headers=headers,
        json={
            'amount': 1000,
            'currency': 'TZS',
            'msisdn': '255680185784',
            'provider': 'AIRTEL',
            'customer_name': 'Test User'
        }
    )
    
    data = response.json()
    if data['success']:
        payment_id = data['data']['id']
        transaction_id = data['data']['transaction_id']
        print(f'Payment initiated: {payment_id}')
        print(f'Transaction ID: {transaction_id}')
        
        # Wait 15 seconds
        print('Waiting 15 seconds...')
        time.sleep(15)
        
        # Check status
        return check_payment_status(payment_id)
    
    return None

# Step 2: Check payment status
def check_payment_status(payment_id, max_attempts=5):
    for attempt in range(max_attempts):
        response = requests.get(
            f'{BASE_URL}/payments/{payment_id}/status/',
            headers=headers
        )
        
        data = response.json()
        if data['success']:
            status = data['data']['status']
            print(f'Attempt {attempt + 1}: Status = {status}')
            
            if status == 'success':
                print('Payment successful!')
                print(f"Transaction ID: {data['data']['transaction_id']}")
                return data
            elif status == 'failed':
                print(f"Payment failed: {data['data']['error_message']}")
                return data
            elif status == 'processing':
                print('Still processing, checking again in 10 seconds...')
                time.sleep(10)
                continue
        
    print('Max attempts reached')
    return None

# Run the payment
if __name__ == '__main__':
    initiate_payment()
```

---

## Polling Strategy

### Recommended Timing

1. **Initial Wait**: 15-30 seconds after initiation
2. **Polling Interval**: Check every 10 seconds
3. **Max Attempts**: 5-6 attempts (total ~60 seconds)
4. **Timeout**: Consider payment failed if no response after 60-90 seconds

### Example Polling Logic

```javascript
async function pollPaymentStatus(paymentId, maxAttempts = 6) {
  for (let attempt = 1; attempt <= maxAttempts; attempt++) {
    console.log(`Checking payment status (attempt ${attempt}/${maxAttempts})...`);
    
    const response = await fetch(
      `http://localhost:8000/api/v1/payments/${paymentId}/status/`,
      {
        headers: { 'Authorization': 'Bearer ' + token }
      }
    );
    
    const data = await response.json();
    
    if (data.success) {
      const status = data.data.status;
      
      if (status === 'success') {
        return { success: true, message: 'Payment completed', data: data.data };
      } else if (status === 'failed') {
        return { success: false, message: 'Payment failed', data: data.data };
      }
      
      // Still processing, wait 10 seconds before next attempt
      if (attempt < maxAttempts) {
        await new Promise(resolve => setTimeout(resolve, 10000));
      }
    }
  }
  
  return { success: false, message: 'Payment timeout - status unknown' };
}
```

---

## Webhook Alternative (Recommended for Production)

Instead of polling, configure a webhook callback URL:

```json
{
  "amount": 1000,
  "currency": "TZS",
  "msisdn": "255680185784",
  "provider": "AIRTEL",
  "customer_name": "Test User",
  "callback_url": "https://yourdomain.com/api/v1/callback/"
}
```

ClickPesa will automatically notify your callback URL when payment status changes.

**Webhook Endpoint:** `/api/v1/callback/` (already implemented)

---

## Troubleshooting

### Transaction ID is NULL

**Problem:** `transaction_id` field is empty in database

**Solution:** The system now automatically extracts and saves the `id` field from ClickPesa's response. Make sure you're running the updated code.

### Payment Stuck in "Processing"

**Possible Causes:**
1. Customer hasn't approved the USSD prompt yet
2. Network issues on customer's side
3. Mobile money service is down

**Action:** Continue polling for 60-90 seconds, then consider it timed out.

### Status Check Returns Error

**Check:**
1. Valid payment ID
2. Bearer token is valid
3. Payment belongs to authenticated user

---

## API Endpoints Summary

| Endpoint | Method | Description | Auth Required |
|----------|--------|-------------|---------------|
| `/api/v1/payments/` | POST | Initiate payment | Yes |
| `/api/v1/payments/{id}/status/` | GET | Check payment status | Yes |
| `/api/v1/payments/{id}/` | GET | Get payment details | Yes |
| `/api/v1/payments/{id}/retry/` | POST | Retry failed payment | Yes |
| `/api/v1/callback/` | POST | Webhook callback | No |

---

## Best Practices

1. ✅ **Always wait 15-30 seconds** before first status check
2. ✅ **Use polling with intervals** (don't hammer the API)
3. ✅ **Set maximum attempts** to avoid infinite loops
4. ✅ **Show loading state** to users during processing
5. ✅ **Store transaction_id** for reconciliation
6. ✅ **Use webhooks** in production for real-time updates
7. ✅ **Handle timeouts gracefully** - payment might still succeed later
8. ✅ **Log all responses** for debugging and reconciliation

---

## Security Notes

- All payment endpoints require authentication (Bearer token)
- Webhook endpoint (`/callback/`) is public but should verify signatures in production
- Never expose API keys or tokens in frontend code
- Use HTTPS in production

---

## Support

For issues or questions:
1. Check Django server logs for detailed error messages
2. Verify ClickPesa credentials in `.env` file
3. Ensure customer has sufficient balance
4. Contact ClickPesa support for provider-specific issues
