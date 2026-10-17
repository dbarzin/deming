# **Datenmodell**

Die wichtigsten Tabellen sind:

- attributes  
- domains  
- controls  
- measures  
- documents  
- risks  

> `controls` = **Sicherheitsmaßnahmen** (umzusetzende Anforderungen).  
> `measures` = **Kontrollen** (periodische Überprüfungen dieser Anforderungen).  
> `risks` = **Informationssicherheitsrisiken** (Register nach ISO 27001 §6.1.2).

## **Abhängigkeiten zwischen Tabellen**

Überblick: wer verwendet was.

```mermaid
flowchart LR
    controls --> |"domain_id (0:1)"| domains
    controls <-->|"control_measure (N:N)"| measures
    attributes -.-o |"optional"| controls
    attributes -.-o |"optional"| measures
    measures -->|"measure_id (0:1)"| measures
    documents -->|"document_id (1:1)"| measures
    risks -->|"owner_id (0:1)"| users
    risks <-->|"control_risk (N:N)"| controls
```

Das folgende detaillierte Schema beschreibt die Felder jeder Tabelle.

```mermaid
erDiagram
    domains ||--o{ controls : "domain_id"
    controls }o--o{ measures : "many-to-many"
    attributes }o--o{ controls : "optional"
    attributes }o--o{ measures : "optional"
    measures o|--o| measures : "next_id"
    documents }o--o| measures : "optional"
    risks }o--o{ controls : "control_risk"
    users ||--o{ risks : "owner_id"

    domains {
        int id PK
        string framework
        string title
        string description
    }
    attributes {
        int id PK
        string name
        string values
    }
    controls {
        int id PK
        int domain_id FK
        string name
        string clause
        string objective
        array measures
        array attributes
        array risks
    }
    measures {
        int id PK
        int next_id FK
        string name
        int periodicity
        date plan_date
        date realisation_date
        int status
        array controls
        array attributes
    }
    documents {
        int id PK
        int measure_id FK
    }
    risks {
        int id PK
        int owner_id FK
        string name
        string status
        int probability
        int impact
        int review_frequency
        date next_review_at
        array controls
    }
```

Die Beziehungen sind wie folgt:

| Verknüpfung | Typ | Beschreibung |
| --- | --- | --- |
| `domains` → `controls` | Fremdschlüssel (1:N) | Jede Sicherheitsmaßnahme verweist über `domain_id` auf ihre Domäne |
| `controls` ↔ `measures` | Viele-zu-viele (bidirektional) | Jede Sicherheitsmaßnahme listet ihre Kontrollen in `measures[]`; jede Kontrolle listet ihre Sicherheitsmaßnahmen in `controls[]` |
| `attributes` → `controls` | Optional | Das Feld `attributes` einer Sicherheitsmaßnahme kann eine Liste von Attribut-IDs enthalten |
| `attributes` → `measures` | Optional | Ebenso für die Kontrollen |
| `measures` → `measures` | Selbstreferenz über `next_id` | Ermöglicht die Verkettung aufeinanderfolgender Kampagnen derselben Kontrolle |
| `documents` → `measures` | Optional (1:N) | Dokumente und Nachweise sind über `measure_id` an die Kontrollen angehängt |
| `risks` ↔ `controls` | Viele-zu-viele über `control_risk` | Ein gemindertes Risiko muss mit einer oder mehreren Sicherheitsmaßnahmen verknüpft sein; die Liste wird im Risiko in `controls[]` gespeichert |
| `users` → `risks` | Optional (1:N) | Jedem Risiko kann ein Verantwortlicher (`owner_id`) für die periodische Überprüfung zugewiesen werden |

> **Hinweis:** Für die Beziehung controls/measures gibt es keine offengelegte Verknüpfungstabelle.  
> Die IDs sind auf beiden Seiten direkt in jedem Objekt enthalten.  
> Die Pivot-Tabelle `control_risk` verknüpft die Risiken mit den Sicherheitsmaßnahmen.

---

## **attributes**

Attribute sind mehrwertige Klassifizierungsreferenzen.  
Jedes Attribut definiert eine Menge von Tags (mit dem Präfix `#`), die Sicherheitsmaßnahmen und Kontrollen zugeordnet werden können.

| Feld | Typ | Beschreibung |
| --- | --- | --- |
| `id` | integer | Eindeutige Kennung (PK) |
| `name` | string | Name der Taxonomie (z. B. *Sicherheitsmaßnahmen*, *Risk_Level*) |
| `values` | string | Durch Leerzeichen getrennte Liste möglicher Werte, jeweils mit dem Präfix `#` (z. B. `#Preventive #Detective #Corrective`) |
| `created_at` | datetime | Erstellungsdatum (ISO 8601, UTC) |
| `updated_at` | datetime | Datum der letzten Änderung |

Beispiel:

```json
{
  "id": 1,
  "name": "Security measures",
  "values": "#Preventive #Detective #Corrective",
  "created_at": "2026-05-17T20:35:52.000000Z",
  "updated_at": "2026-05-17T20:35:52.000000Z"
}
```

---

## **domains**

Domänen gruppieren die Sicherheitsmaßnahmen nach Themenbereich.  
Jede Domäne gehört zu einem regulatorischen oder methodischen Rahmenwerk (`framework`).

| Feld | Typ | Beschreibung |
| --- | --- | --- |
| `id` | integer | Eindeutige Kennung (PK) |
| `framework` | string | Referenzrahmenwerk (z. B. `NIS2`, `Vulnerability Management`) |
| `title` | string | Name der Domäne (z. B. *NIS2 Governance and Steering*) |
| `description` | string | Beschreibung des abgedeckten Bereichs, oft mit Verweis auf einen Artikel oder eine Norm |
| `created_at` | datetime | Erstellungsdatum |
| `updated_at` | datetime | Datum der letzten Änderung |

Beispiel:

```json
{
  "id": 1,
  "framework": "NIS2",
  "title": "NIS2 Governance and Steering",
  "description": "Strategic and operational steering according to Art. 21.1 and 21.2.a",
  "created_at": "2026-05-17T20:35:52.000000Z",
  "updated_at": "2026-05-17T20:35:52.000000Z"
}
```

---

## **controls**

Sicherheitsmaßnahmen beschreiben die umzusetzenden Anforderungen.  
Jede Sicherheitsmaßnahme gehört zu einer Domäne und wird durch eine oder mehrere Kontrollen überprüft.

| Feld | Typ | Beschreibung |
| --- | --- | --- |
| `id` | integer | Eindeutige Kennung (PK) |
| `domain_id` | integer | Verweis auf `domains.id` (FK, Pflichtfeld) |
| `name` | string | Name der Sicherheitsmaßnahme, oft mit Artikelnummer (z. B. *Art.21.2.a – Risk Analysis*) |
| `clause` | string | Kurzkennung der normativen Klausel (z. B. `NIS2-Art.21.2.a`) |
| `objective` | string | Erwartetes Ziel dieser Sicherheitsmaßnahme |
| `input` | string \| null | Für die Umsetzung erforderliche Daten oder Ressourcen |
| `model` | string \| null | Empfohlenes Betriebsmodell oder empfohlene Methode |
| `indicator` | string \| null | Strukturierter Leistungsindikator (Ziel, Häufigkeit, Verantwortlicher) |
| `action_plan` | string \| null | Zugehöriger Aktions- oder Behandlungsplan |
| `attributes` | array \| null | Liste der zugeordneten Attribut-IDs; `null`, wenn keine |
| `measures` | array | Liste der IDs der Kontrollen, die diese Sicherheitsmaßnahme überprüfen |
| `created_at` | datetime | Erstellungsdatum |
| `updated_at` | datetime | Datum der letzten Änderung |

Beispiel:

```json
{
  "id": 1,
  "domain_id": 1,
  "name": "Art.21.2.a - Risk Analysis",
  "clause": "NIS2-Art.21.2.a",
  "objective": "Assessment of threats to critical assets using EBIOS RM or equivalent methodology",
  "input": "List of critical assets, EBIOS RM methodology",
  "model": "Annual analysis according to ISO 27005 or EBIOS RM",
  "indicator": "Target: Residual score ≤ acceptable | Frequency: Annual | Owner: CISO",
  "action_plan": "Risk treatment plan approved by Management",
  "attributes": null,
  "measures": [1]
}
```

---

## **measures**

Kontrollen beschreiben periodische operative Überprüfungen.  
Eine Kontrolle prüft, ob eine oder mehrere Sicherheitsmaßnahmen ordnungsgemäß angewendet werden.  
Sie enthält Planungs-, Durchführungs- und Ergebnisdaten.

| Feld | Typ | Beschreibung |
| --- | --- | --- |
| `id` | integer | Eindeutige Kennung (PK) |
| `name` | string | Titel der Überprüfung |
| `objective` | string \| null | Spezifisches Ziel dieser Kontrolle |
| `input` | string \| null | Für die Durchführung erforderliche Daten oder Nachweise |
| `model` | string \| null | Vorgehensweise |
| `action_plan` | string \| null | Korrekturmaßnahmen, wenn die Kontrolle fehlschlägt |
| `periodicity` | integer \| null | Häufigkeit in Monaten (z. B. `12` = jährlich, `3` = quartalsweise) |
| `plan_date` | date \| null | Geplantes Durchführungsdatum (`YYYY-MM-DD`) |
| `realisation_date` | date \| null | Tatsächliches Durchführungsdatum |
| `observations` | string \| null | Freie Kommentare zum Ergebnis |
| `score` | number \| null | Numerischer Score der Bewertung; `null`, wenn noch nicht durchgeführt |
| `note` | number \| null | Zusätzliche qualitative Note |
| `status` | integer | Aktueller Status der Kontrolle (siehe unten) |
| `next_id` | integer \| null | ID der nächsten Kontrolle in der historischen Kette (Selbst-FK) |
| `attributes` | array \| null | Liste der zugeordneten Attribut-IDs; `null`, wenn keine |
| `scope` | string \| null | Geltungsbereich (Einheit, Standort, System) |
| `controls` | array | Liste der IDs der von dieser Kontrolle überprüften Sicherheitsmaßnahmen |
| `created_at` | datetime | Erstellungsdatum |
| `updated_at` | datetime | Datum der letzten Änderung |

### **Werte des Feldes `status`**

| Wert | Bedeutung |
| --- | --- |
| `0` | Zu erledigen / noch nicht durchgeführt (`realisation_date` ist null) |
| `1` | Vorgeschlagen (der Auditierte hat ein Ergebnis eingereicht, Validierung ausstehend) |
| `2` | Erledigt / abgeschlossen (`realisation_date` ist gesetzt) |

Beispiel:

```json
{
  "id": 1,
  "name": "Formal review and signature of the risk analysis",
  "objective": "Management validation of the risk treatment strategy",
  "model": "Executive Committee presentation + formal signature",
  "periodicity": 12,
  "plan_date": "2026-07-31",
  "realisation_date": "2025-03-25",
  "score": null,
  "status": 2,
  "next_id": null,
  "attributes": null,
  "scope": null,
  "controls": [1]
}
```

---

## **documents**

Die Tabelle `documents` speichert die Anhänge und dokumentarischen Nachweise der Kontrollen.  
Jedes Dokument ist über `measure_id` mit einem Datensatz `measures` verknüpft.

---

## **risks**

Die Risikoliste erfasst die Informationssicherheitsrisiken gemäß ISO 27001:2022 §6.1.2 und §8.2.  
Jedes Risiko wird nach einer konfigurierbaren Bewertungsmethode bewertet, optional einem Verantwortlichen zugewiesen und einem periodischen Überprüfungszyklus unterzogen.  
Ein Risiko mit Status *Gemindert* muss über die Pivot-Tabelle `control_risk` mit mindestens einer Sicherheitsmaßnahme verknüpft sein.

| Feld | Typ | Beschreibung |
| --- | --- | --- |
| `id` | integer | Eindeutige Kennung (PK) |
| `name` | string | Kurze, eindeutige Bezeichnung des Risikos (Pflichtfeld) |
| `description` | string \| null | Ausführliche Beschreibung des Risikos |
| `owner_id` | integer \| null | Verweis auf `users.id` — für die periodische Überprüfung verantwortliche Person |
| `probability` | integer | Wahrscheinlichkeitsstufe (1 bis N, Standardformeln) oder Bedrohungsstufe bei der Formel MONARC |
| `probability_comment` | string \| null | Freier Kommentar zur Bewertung der Wahrscheinlichkeit |
| `impact` | integer | Schweregrad, wenn das Risiko eintritt (1 bis N) |
| `impact_comment` | string \| null | Freier Kommentar zur Bewertung der Auswirkung |
| `exposure` | integer \| null | Erreichbarkeit des Systems (nur Formel BSI 200-3: 0 = offline, 1 = intern, 2 = Internet) |
| `vulnerability` | integer \| null | Ausnutzbarkeit bekannter Schwachstellen (Formel BSI 200-3) oder Verwundbarkeitsstufe (Formel MONARC) |
| `residual_risk` | integer \| null | Manuell eingegebenes Restrisiko (nur Formel MONARC) |
| `status` | enum | Behandlungsentscheidung (siehe Werte unten) |
| `status_comment` | string \| null | Freier Kommentar zur Behandlungsentscheidung |
| `review_frequency` | integer | Intervall in Monaten zwischen zwei Überprüfungen (Standard: 12) |
| `next_review_at` | date \| null | Geplantes Datum der nächsten Überprüfung |
| `controls` | array | Liste der mit diesem Risiko verknüpften Sicherheitsmaßnahmen-IDs (über `control_risk`) |
| `created_at` | datetime | Erstellungsdatum |
| `updated_at` | datetime | Datum der letzten Änderung |
| `deleted_at` | datetime \| null | Datum der logischen Löschung |

### **Werte des Feldes `status`**

| Wert | Bedeutung |
| --- | --- |
| `not_evaluated` | Noch nicht bewertet |
| `not_accepted` | Nicht akzeptiert — ein verknüpfter Aktionsplan ist erforderlich |
| `temporarily_accepted` | Vorläufig akzeptiert |
| `accepted` | Akzeptiert |
| `mitigated` | Gemindert — mindestens eine verknüpfte Sicherheitsmaßnahme ist erforderlich |
| `transferred` | Übertragen (Versicherung, Dritte) |
| `avoided` | Vermieden |

Beispiel:

```json
{
  "id": 1,
  "name": "Unauthorised access to sensitive data",
  "description": "Risk of data exfiltration via compromised credentials.",
  "owner_id": 3,
  "probability": 3,
  "impact": 4,
  "exposure": null,
  "vulnerability": null,
  "residual_risk": null,
  "status": "mitigated",
  "status_comment": "Access control policy deployed in Q1.",
  "review_frequency": 12,
  "next_review_at": "2027-06-01",
  "controls": [12, 15],
  "created_at": "2026-06-09T10:00:00.000000Z",
  "updated_at": "2026-06-09T10:00:00.000000Z",
  "deleted_at": null
}
```
