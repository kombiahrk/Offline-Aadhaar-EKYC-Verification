# Offline-Aadhaar-EKYC-Verification
Offline Aadhaar EKYC Verification
Don't Delete extracted-xml & tmp-xml

# Getting Started

The root folder contains all the files to get started with uploading aadhar XML files (https://resident.uidai.gov.in/offline-kyc) and verify them and extract details from it.

Aadhaar Paperless Offline e-kyc
Introduction
UIDAI has launched Aadhaar Paperless Offline e-KYC Verification to allow Aadhaar number holders to voluntarily use it for establishing their identity in various applications in paperless and electronic fashion, while still maintaining privacy, security and inclusion.

Why Aadhaar Paperless Offline e-KYC ?
UIDAI provides a mechanism to verify identity of an Aadhaar number holder through an online electronic KYC service. The e-KYC service provides an authenticated instant verification of identity and significantly lowers the cost of paper based verification and KYC. However, this method of online e-KYC is not available to all agencies and may not be suitable due to some of the following reasons;

Online e-KYC requires reliable connectivity
Agency needs to have technical infrastructure to call online e-KYC service and deploy devices (as necessary)
The resident may need to provide biometrics for the online e-KYC
UIDAI maintains a record of the KYC request for audit purposes
Advantages of Aadhaar Paperless Offline e-KYC
Privacy :
KYC data may be shared by the Aadhaarnumber holder directly without the knowledge of UIDAI.
Aadhaar number of the resident is not revealed, instead only a reference ID is shared.
No core biometrics (fingerprints or iris) required for such verification
Aadhaar number holder gets a choice of the data (among the demographics data and photo) to be shared.
Security:
Aadhaar KYC data downloadable by Aadhaar number holder is digitally signed by UIDAI to verify authenticity and detect any tampering.
Agency can validate the data through their own OTP/Face Authentication.
KYC data is encrypted with the phrase provided by Aadhaar number holder allowing residents control of their data.
Inclusion:
Aadhaar Paperless Offline e-KYC is voluntary and Aadhaar number holder driven.
Any agency working with people can use it with consent of the Aadhaar number holder allowing wide usage.
How does it work?
Aadhaar Paperless Offline e-KYC eliminates the need for the resident to provide photo copy of Aadhaar letter and instead resident can download the KYC XML and provide the same to agencies wanted to have his/her KYC. The agency can verify the KYC details shared by the resident in a manner explained in below sections. The KYC details is in machine readable XML which is digitally signed by UIDAI allowing agency to verify its authenticity and detect any tampering. The agency can also authenticate the user through their own OTP/Face authentication mechanisms.

How to obtain Aadhaar Paperless Offline e-KYC Data
Aadhaar number holders can obtain Aadhaar Paperless Offline e-KYC data through the following channels:

Download Aadhaar Paperless Offline e-KYC from resident portal (https://resident.uidai.gov.in)
In future, obtain Aadhaar Paperless Offline e-KYC will also be available via:
mAadhaarmobile application on a registered phone number
Inbound SMS using registered phone number
Aadhaar Kendra using Biometric Authentication
What Data is covered in e-KYC
While downloading/obtaining offline e-KYC data, following fields are included in the XML.

Resident Name
Download Reference Number
Address
Photo
Gender
DoB/YoB
Mobile Number (in hashed form)
Email (in hashed form)
Aadhaar Paperless Offline e-KYC data is encrypted using a “Share Phrase” provided by the Aadhaar number holder at the time of downloading which is required to be shared with agencies to read KYC data.

How to share Aadhaar Paperless Offline e-KYC Data
Aadhaar Paperless Offline e-KYC data may be provided to the verifying agency by the Aadhaar number holder in digital or physical format along with share phrase:

Digital Format: XML/PDF
This format is preferred when high quality photo is required
Printed Format: QR code
When resident is more comfortable with a physically printed format
Low resolution photo for visual inspection only
