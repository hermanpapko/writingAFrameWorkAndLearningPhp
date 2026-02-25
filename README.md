The application is designed to analyze the target audience of a particular application or group of applications. A number of applications send data to the service for analysis and aggregation in the form of a CSV file. The main task of the application is to parse this file, aggregate information, and display it on the front end.
 

Task: 

Create a CSV file with the following structure: “country”, “city”, “isActive” (boolean), “gender”, “birthDate”, “salary”, “hasChildren”, ‘familyStatus’, “registrationDate”.
Create a simple PHP script that parses this CSV file and saves the data to a database. Use pdo to save the data to the database.
Create a PHP script that returns all rows matching a specific criterion based on get parameters and displays the result on the screen. The information can be filtered by any of the fields listed above or by a combination of several fields. To filter by date, use a range search.
 

Additional information: customers may send files containing data that could potentially negatively impact the database. Learn more about SQL injections and how to avoid them.
