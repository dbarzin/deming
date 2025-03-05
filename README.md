# Deming

[![Latest Release](https://img.shields.io/github/release/dbarzin/deming.svg?style=flat-square)](https://github.com/dbarzin/deming/releases/latest)
![License](https://img.shields.io/github/license/dbarzin/deming.svg?style=flat-square)
![Contributors](https://img.shields.io/github/contributors/dbarzin/deming.svg?style=flat-square)
![Stars](https://img.shields.io/github/stars/dbarzin/deming?style=flat-square)

- Read this in other languages: [French](README.fr.md)

## :rocket: Introduction

In a world where information security is more critical than ever, organizations must not only implement security measures, but also ensure that they are effective and efficient. **Deming** is here to help you meet this challenge head on.

### :question: What is Deming?

**Deming** is a powerful, intuitive tool designed for managing, planning, monitoring and reporting on the effectiveness of security measures. In line with ISO/IEC 27001:2013, Chapter 9, **Deming** helps you guarantee appropriate and proportionate security, while complying with the most demanding standards.

### :dart: Why monitor?

Regular monitoring and evaluation of security measures is essential for :

- Evaluate the effectiveness of controls in place.
- Verify that security requirements are being met.
- Continuously improve information security.
- Provide accurate data for decision-making.
- Justify the need to improve the information security management system (ISMS).

**Deming** gives you the tools you need to meet these objectives effectively.

### :chart_with_upwards_trend: Performance assessment

According to ISO 27001, chapter 9.1, it is imperative to assess security performance. **Deming** guides you through this process, enabling you to:

- Determine what needs to be monitored and measured.
- Choose the right methods to ensure valid results.
- Schedule monitoring and measurement times.
- Identify who is responsible for each task.
- Analyze and evaluate results.

## :computer: Screen overview

### :star: Main screen

[<img src="public/screenshots/main1.png" width="500">](public/screenshots/main1.png)

[<img src="public/screenshots/main2.png" width="500">](public/screenshots/main2.png)

### :white_check_mark: List of controls

[<img src="public/screenshots/controls.png" width="400">](public/screenshots/controls.png)

### :calendar: Control planning

[<img src="public/screenshots/calendar.png" width="450">](public/screenshots/calendar.png)

### :memo: Action plan management

[<img src="public/screenshots/plans.png" width="450">](public/screenshots/plans.png)

### :satellite: Protective measures coverage view

[<img src="public/screenshots/radar.png" width="500">](public/screenshots/radar.png)

### :page_facing_up:️ ISMS steering meeting report

[<img src="public/screenshots/pilotage1.png" width="400">](public/screenshots/pilotage1.png)
[<img src="public/screenshots/pilotage2.png" width="400">](public/screenshots/pilotage2.png)

## :books: Documentation

To find out more about using the application, please refer to the [user documentation](https://dbarzin.github.io/deming).

## :hammer_and_wrench:️ Technologies used

- **Languages**: PHP, JavaScript
- **Framework** : Laravel
- **Database**: MariaDB, MySQL, PostgreSQL, and SQLite
- **Graphics**: ChartJS

## ⚙️ Installation

Follow the [installation procedure for Debian](https://github.com/dbarzin/deming/blob/main/INSTALL.debian.md) to set up the application.

Follow the [installation procedure for Ubuntu](https://github.com/dbarzin/deming/blob/main/INSTALL.md) to set up the application.

### 🐳 Docker Installation

Get up and running quickly using Docker. Run a local instance in development mode:

```bash
git clone https://github.com/dbarzin/deming.git
cd deming
cp .env.example .env
sed -i 's/DB_HOST=127.0.0.1/DB_HOST=mysql/' .env
docker compose up
```

## :car: Roadmap

Consult the [roadmap](https://github.com/dbarzin/deming/blob/main/ROADMAP.md) to discover future developments of **Deming**.

## :scroll: License

**Deming** is open source software distributed under the [GPL](https://www.gnu.org/licenses/licenses.html) license. Contribute, improve and participate in securing information systems worldwide!
