# nuke-php

## Domain Verify 
- .well-known file
    ```mermaid
    sequenceDiagram
        participant nuke as Nuke
        participant service as Service Domain
    
        note over service: create {your service domain}/.well-known/nuke-verify/{nuke token}<br><br>payload:<br>{nuke token}
    
        nuke ->> service: GET {your service domain}/.well-known/nuke-verify/{nuke token}
    
        service -->> nuke: headers:<br>Status-Code: 200 OK<br><br>payload:<br>{nuke token}
    
        note over service: delete {your service domain}/.well-known/nuke-verify/{nuke token}
    ```
- DNS 
    ```mermaid
    sequenceDiagram
        participant nuke as Nuke
        participant service as Service Domain
    
        note over service: create TXT DNS for _nuke_verify.{your service domain}<br><br>payload:<br>{nuke token}
    
        nuke ->> service: CHECK TXT DNS _nuke_verify.{your service domain} for {nuke token}
    
        service -->> nuke: Domain is verified
    
        note over service: delete TXT DNS for _nuke_verify.{your service domain}
    ```

## Browser & Webhook Authorize Event

```mermaid
sequenceDiagram
    participant nuke as Nuke
    participant service as Service
    participant service_api as Service API

    nuke ->> service: {service url}<br>?redirect_uri={nuke app url}<br>&nuke_identifier={nuke identifier}<br>&nuke_token={nuke token}

    note over service: nuke_token: Nuke::decrypt()

    note over service: service_token: generated by your service

    service ->> nuke: {nuke app url}<br>?nuke_identifier={nuke identifier}<br>&nuke_token={nuke token}<br>&service_token={service token}

    nuke ->> service_api: POST {your service api webhook url}<br><br>headers:<br>X-Nuke-Identifier: {nuke identifier}<br>X-Nuke-Signature: t={current time}, v={signature}<br><br>payload (json):<br>{event: {type: authorize, data: {token: {service token}}}}

    note over service_api: Nuke::verifyIdentifierAndSignature()

    note over service_api: verify/identify and set as active service_token

    service_api -->> nuke: headers:<br>Status-Code: 204 No Content
```

## Webhook Revoke Event

```mermaid
sequenceDiagram
    participant nuke as Nuke
    participant service_api as Service API

    nuke ->> service_api: POST {your service api webhook url}<br><br>headers:<br>X-Nuke-Identifier: {nuke identifier}<br>X-Nuke-Signature: t={current time}, v={signature}<br><br>payload (json):<br>{event: {type: revoke, data: {token: {service token}}}}

    note over service_api: Nuke::verifyIdentifierAndSignature()

    note over service_api: verify/identify and set as revoked service_token

    service_api -->> nuke: headers:<br>Status-Code: 204 No Content
```

## Webhook Nuke Event

```mermaid
sequenceDiagram
    participant nuke as Nuke
    participant service_api as Service API

    nuke ->> service_api: POST {your service api webhook url}<br><br>headers:<br>X-Nuke-Identifier: {nuke identifier}<br>X-Nuke-Signature: t={current time}, v={signature}<br><br>payload (json):<br>{event: {type: nuke, data: {token: {service token}}}}

    note over service_api: Nuke::verifyIdentifierAndSignature()

    note over service_api: verify/identify and perform nuke action based on service_token

    service_api -->> nuke: headers:<br>Status-Code: 204 No Content
```
