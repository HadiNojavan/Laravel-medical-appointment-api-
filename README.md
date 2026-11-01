# Medical Appointment API

Base URL

```
http://127.0.0.1:8000/api
```

Headers for every request

```
Accept: application/json
Content-Type: application/json
Authorization: Bearer <token>
```

Authorization is not needed for register and login.

---

## Auth

### Register

```
POST /register
```

```
{
  "name": "Ali Rezaei",
  "email": "ali@example.com",
  "password": "password123"
}
```

### Login

```
POST /login
```

Admin

```
{
  "email": "hadi@gmail.com",
  "password": "hadi123"
}
```

Doctor

```
{
  "email": "doctor@gmail.com",
  "password": "doctor123"
}
```

Patient

```
{
  "email": "test@gmail.com",
  "password": "password"
}
```


### Logout

```
DELETE /logout
```

### Current user

```
GET /me
```

---

## Patient

### List doctors

```
GET /doctors
```

Query (all optional)

```
specialty_id=1
search=sara
per_page=15
page=1
```

### Free slots of a doctor

```
GET /doctors/{doctor}/slots?from=2026-11-01&to=2026-11-07
```

### Book an appointment

```
POST /appointments
```

```
{
  "doctor_id": 1,
  "start_at": "2026-11-01T05:30:00Z"
}
```

---

## Doctor

### Show profile

```
GET /doctor/profile
```

### Update profile

```
PATCH /doctor/profile
```

```
{
  "name": "Dr. Brown",
  "bio": "Cardiologist with 12 years of experience"
}
```

### List availabilities

```
GET /doctor/availabilities
```

### Create availability

```
POST /doctor/availabilities
```

```
{
  "rrule": "FREQ=WEEKLY;BYDAY=SA,SU,MO,TU,WE",
  "start_time": "09:00",
  "end_time": "13:00",
  "slot_duration_minutes": 30,
  "valid_from": "2026-11-01",
  "valid_until": null,
  "timezone": "Asia/Tehran"
}
```

### Show availability

```
GET /doctor/availabilities/{availability}
```

### Update availability

```
PATCH /doctor/availabilities/{availability}
```

```
{
  "rrule": "FREQ=WEEKLY;BYDAY=SA,MO",
  "start_time": "09:00",
  "end_time": "12:00",
  "slot_duration_minutes": 20,
  "valid_from": "2026-11-01",
  "valid_until": "2027-03-20",
  "timezone": "Asia/Tehran",
  "is_active": true
}
```

All fields are optional, send only what you want to change.

### Deactivate availability

```
DELETE /doctor/availabilities/{availability}
```

---

## Admin

### Create doctor

```
POST /admin/doctors
```

```
{
  "name": "Dr. Sara Ahmadi",
  "email": "sara.ahmadi@example.com",
  "password": "Secret1234",
  "specialty_id": 1,
  "medical_license_number": "123456",
  "bio": "Cardiologist"
}
```

### List doctors

```
GET /admin/doctors
```

Query (all optional)

```
specialty_id=1
is_active=1
search=sara
per_page=15
page=1
```

### Show doctor

```
GET /admin/doctors/{doctor}
```

### Update doctor

```
PATCH /admin/doctors/{doctor}
```

```
{
  "name": "Dr. Sara Ahmadi",
  "email": "sara.new@example.com",
  "specialty_id": 2,
  "medical_license_number": "654321",
  "bio": "Updated bio"
}
```

All fields are optional, send only what you want to change.

### Activate or deactivate doctor

```
PATCH /admin/doctors/{doctor}/status
```

```
{
  "is_active": false
}
```
