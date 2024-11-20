File-O-Manager
Project Description
File-O-Manager is an open and flexible file management solution that aims to address the limitations of many existing solutions in terms of accessibility, functionality, and cost. This project aims to facilitate efficient and secure file management, also offering users the ability to customize and expand it according to their needs.

File-O-Manager offers an attractive alternative for both those seeking a free option and those wanting advanced features and professional support through a commercial scheme. This hybrid approach allows the software to be accessible to everyone, regardless of their resources or needs.

Key Features
File Upload and Download: Quickly and securely upload and download files.
Directory Management: Organize and manage folders and files through an intuitive interface.
User Permission Control: Granular permission management based on user roles (administrator, common user, guest).
Modern Web Interface: Web interface developed using HTML, CSS, and JavaScript to enhance interaction.
Technologies Used
Backend: PHP to handle server-side logic.
Database: MariaDB to store information on users, permissions, and files.
Frontend: HTML, CSS, JavaScript.
Server: Debian with Apache.
Installation
To start using File-O-Manager, follow these steps:

Clone the Repository:
bash
Copiar código
git clone https://github.com/IgorGoP/File-O-Manager.git
Set Up the Environment:
Ensure you have an Apache server with PHP and MariaDB configured.
Configure the environment files in /config/ to set up the database connections.
Initialize the Database:
Run the SQL commands provided in the docs/database.sql file to create the necessary tables.
Set Up Dependencies:
Use Composer to install project dependencies:
bash
Copiar código
composer install
Project Structure
The project structure is as follows:

/backend/ - Contains the PHP code to handle server-side logic.
/frontend/ - Includes the HTML, CSS, and JavaScript for the user interface.
/config/ - Configuration files like .env to define environment variables.
/docs/ - Project documentation, including installation and usage instructions.
/public/ - Public resources such as images, scripts, or static files.
License
This project is licensed under the MIT License, with additional terms for commercial use and donation acceptance. This means that anyone can use, modify, and distribute the software as long as they include the original license and adhere to the following additional terms:

Free and Paid Schemes: File-O-Manager has a free version with basic features and a paid version with advanced features.
Donation Acceptance: Voluntary donations are accepted to support project development.
Revenue Sharing: If other developers or companies generate revenue from using the code, they must share a percentage of that revenue with the original author.
For more details, refer to the LICENSE.md file.

Contributing
Contributions are welcome. If you wish to contribute to the development of File-O-Manager, follow these steps:

Fork the Repository.
Create a Branch for Your Feature (git checkout -b feature/NewFeature).
Commit Your Changes (git commit -m 'Added new feature').
Push the Branch (git push origin feature/NewFeature).
Create a Pull Request.
Contact
For questions or additional assistance, you can contact the project creator through their GitHub profile: https://github.com/IgorGoP

Project Link
The project is published on GitHub and can be accessed at the following link: https://github.com/IgorGoP/File-O-Manager
