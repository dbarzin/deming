## Setup

### Attributes <a name="tags"></a>

This screen allows you to manage the attributes associated with the security measures.
It contains the list of attributes and allows you to create, delete or modify attribute lists.

[![Screenshot](images/tags.png)](images/tags.png)

### Domains <a name="domains"></a>

This screen allows you to create, modify or delete lists of security domains.

[![Screenshot](images/domains.png)](images/domains.png)

The application is provided with a security measurement base inspired by the ISO 27001:2022 standard, but it is possible to define new security domains inspired by other standards such as PCIDSS, HDS, etc.

### Users <a name="users"></a>

Users are directly defined in the application.

[![Screenshot](images/users.png)](images/users.png)

There are four different roles:

* RSSI: the RSSI is the administrator of the application. He can create new measurements, new attributes, modify controls already carried out...

* Users: users can use the application without being able to modify the measurements, attributes and controls already carried out.

* Auditee: Auditees can only carry out and see the mesurements that have been assigned to them or that they have carried out previously.

* Auditor: the auditor has read access to all the information in the application.

### Groups <a name=“groups”></a>

This screen is used to define user groups.
A group brings together a set of users and controls.

[![Screenshot](images/groups.png)](images/groups.png)

A group is composed of :

* a group name

* a group description

* a list of users

* a list of controls


### Reports <a name="report"></a>

The application allows you to generate the ISMS management report and to export the list of domains, the security measures and all the checks carried out in an Excell file.

[![Screenshot](images/reports.png){: style="width:500px"}](images/reports.png)

Here is the ISMS pilot report:

[![Screenshot](images/report1.png)](images/report1.png)

[![Screenshot](images/report2.png)](images/report2.png)

### Import <a name="import"></a>

Measures can be imported from an .XLSX file or from the template database.

When importing, all other controls and measures can be deleted and test data generated.

[![Screenshot](images/import.png)](images/import.png)

### Documents <a name="documents"></a>

This screen lets you modify the document management settings used in Deming.

[![Screenshot](images/documents.png){: style="width:400px"}](images/documents.png)

#### Templates

This part of the screen lets you modify the document templates used for control sheets and the ISMS management report, and gives you an overview of all the documents used as proof when carrying out security controls.

#### Storage

This part of the screen lets you check the integrity of documents stored in the application.

By clicking on “Verify”, Deming launches the integrity check of documents stored in the application, using a CRC32.

#### Retention

This part of the screen lets you configure the retention period (in months) for checks carried out in the application.

All checks carried out after this date are deleted, along with the associated documents and action plans.

### Notifications <a name="notifications"></a>

This screen is used to configure the notifications sent to users when they have to carry out controls.

The screen contains:

* The subject of the mail sent to the user;

* The sender of the email;

* The periodicity of sending notifications;

* The notification deadlines.

[![Screenshot](images/config.png){: style="width:500px"}](images/config.png)

When you click on:

* "Save" - the configuration is saved;

* "Test" - a test mail is sent to the current user;

* "Cancel" - you return to the main page.
