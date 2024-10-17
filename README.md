---

# 📚 Project Title: CSV Data Import Assignment

## 🌟 Overview

This project is designed to handle the upload, verification, and processing of CSV files containing financial data for an educational institution. The application provides a user-friendly interface for users to upload CSV files, processes the data, and inserts it into various database tables.

## 🛠️ Key Features

- **File Upload**: Users can upload CSV files of up to 250 MB.
- **Data Validation**: Validates data format and size before processing.
- **Batch Processing**: Efficiently processes large datasets using chunking.
- **Database Integration**: Uses Laravel’s Eloquent ORM for database interactions.
- **Queue Management**: Utilizes Laravel queues for handling background tasks.
- **Error Handling**: Provides clear error messages for user feedback.
- **Dynamic UI**: Displays success and error messages effectively.

## 🚀 Getting Started

### 🔧 Prerequisites

Before running the project, ensure you have the following installed on your machine:

- [PHP](https://www.php.net/downloads) (version 8.0 or higher)
- [Composer](https://getcomposer.org/download/)
- [Laravel](https://laravel.com/docs) (version 8.x or higher)
- [MySQL](https://dev.mysql.com/downloads/mysql/) (version 5.7 or higher)
- [Node.js](https://nodejs.org/en/download/) (for running JavaScript on the server)
- [NPM](https://www.npmjs.com/get-npm) (Node Package Manager)

### 📁 Cloning the Repository

Clone this repository to your local machine using:

```bash
git clone https://github.com/Ankit-khoiwal/PHP-Data-Import-Assignment.git
```

### 📥 Install Dependencies

Navigate to the project directory and install the required PHP dependencies:

```bash
composer install
```

### ⚙️ Environment Configuration

1. **Create a .env file**: 
   Copy the `.env.example` file to a new `.env` file:

   ```bash
   cp .env.example .env
   ```

2. **Generate an application key**:
   Run the following command to generate an application key:

   ```bash
   php artisan key:generate
   ```

3. **Database Configuration**: 
   Update the `.env` file with your database credentials:

   ```plaintext
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

### 🏗️ Running Migrations

Run the database migrations to create the required tables:

```bash
php artisan migrate
```

### 🎶 Seeding Database (Optional)

If you want to seed your database with sample data, you can run:

```bash
php artisan db:seed
```

### 💻 Running the Application

To start the application, run the following command:

```bash
php artisan serve
```

Access the application in your web browser at:

```
http://localhost:8000
```

### 🧑‍💻 Running the Queue Worker

To process queued jobs, run:

```bash
php artisan queue:work
```

This command starts processing jobs in the background.

## 📊 Key Implementations

### 📂 File Upload

The application allows users to upload CSV files using a straightforward form. It validates file size (max 250MB) and type (CSV) to ensure data integrity.

### 🔍 Data Validation

Before any data processing occurs, the application validates each row in the uploaded CSV to ensure it meets the necessary criteria.

### ⚡ Batch Processing

To improve performance, the application uses batch processing to insert data into the database in chunks, reducing memory usage and speeding up database interactions.

### 📈 Database Structure

The application interacts with multiple database tables. Here’s a brief overview of each table:

- **Temporary_completedata**: Stores raw data from uploaded CSV files.
- **FeeCategory**: Contains different fee categories (e.g., Tuition, Sports).
- **FeeCollectionTypes**: Stores types of fee collections (e.g., Academic, Transport).
- **Branches**: Represents different branches of the institution.
- **EntryMode**: Contains various modes of fee entry (e.g., DUE, REVDUE).
- **Module**: Lists the different modules in the system.
- **FeeTypes**: Maps fee categories to branches and types.
- **FinancialTrans**: Records financial transactions linked to students.
- **FinancialTranDetails**: Contains detailed information about each transaction.
- **CommonFeeCollection**: Aggregates common fee collections for reporting.
- **CommonFeeCollectionHeadwise**: Provides a head-wise breakdown of common fees.

### 🗂️ Error Handling

The application employs robust error handling, displaying clear messages to users regarding file upload issues, data verification failures, or processing errors.

## 🎨 UI/UX Design

The application features a clean and intuitive user interface designed to enhance user experience while maintaining functionality.

## 💡 Troubleshooting

If you encounter issues while running the application, consider the following:

- **Ensure Database Configuration**: Double-check your `.env` database settings.
- **Run Queue Worker**: Make sure the queue worker is running to process background jobs.
- **Check Laravel Logs**: Review the `storage/logs/laravel.log` file for any error messages.

## 🤝 Contributing

Contributions are welcome! If you have suggestions for improvements or bug fixes, please submit a pull request.

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Contact

For any queries, feel free to reach out to:

- **Ankit Khatik**: ankitkhatik3@gmail.com

---
