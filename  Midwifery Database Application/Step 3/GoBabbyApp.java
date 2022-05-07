import java.sql.* ;
import java.util.*;
import java.text.SimpleDateFormat;

class GoBabbyApp
{
    public static void main ( String [ ] args ) throws SQLException
    {
        // Unique table names.  Either the user supplies a unique identifier as a command line argument, or the program makes one up.
        int sqlCode=0;      // Variable to hold SQLCODE
        String sqlState="00000";  // Variable to hold SQLSTATE

        // Register the driver.  You must register the driver before you can use it.
        try { DriverManager.registerDriver ( new com.ibm.db2.jcc.DB2Driver() ) ; }
        catch (Exception cnfe){ System.out.println("Class not found"); }

        // This is the url you must use for DB2.
        //Note: This url may not valid now ! Check for the correct year and semester and server name.
        String url = "jdbc:db2://winter2022-comp421.cs.mcgill.ca:50000/cs421";

        //REMEMBER to remove your user id and password before submitting your code!!
        String your_userid = null;
        String your_password = null;
        //AS AN ALTERNATIVE, you can just set your password in the shell environment in the Unix (as shown below) and read it from there.
        //$  export SOCSPASSWD=yoursocspasswd
        if(your_userid == null && (your_userid = System.getenv("SOCSUSER")) == null)
        {
            System.err.println("Error!! do not have a password to connect to the database!");
            System.exit(1);
        }
        if(your_password == null && (your_password = System.getenv("SOCSPASSWD")) == null)
        {
            System.err.println("Error!! do not have a password to connect to the database!");
            System.exit(1);
        }
        Connection con = DriverManager.getConnection (url,your_userid,your_password) ;
        Statement statement = con.createStatement(ResultSet.TYPE_SCROLL_INSENSITIVE, ResultSet.CONCUR_UPDATABLE);

        Scanner sc = new Scanner(System.in);
        ask_pid: while (true) {
            System.out.print("Please enter your practitioner id [E] to exit: ");
            String pid = sc.nextLine();
            if(pid.equals("E"))
                break;

            try {
                String querySQL = "SELECT name FROM midwives WHERE practitioner_id = " + pid;
//                System.out.println(querySQL);
                ResultSet rs = statement.executeQuery(querySQL);

                // check if pid exists in the database
                if (!rs.next())
                {
                    System.out.println("Invalid practitioner id.");
                    continue;   // return to previous menu
                } else
                {
                    String name = rs.getString(1);
                    System.out.println("Midwife's name: " + name);
                }
            } catch (SQLException e) {
                sqlCode = e.getErrorCode(); // Get SQLCODE
                sqlState = e.getSQLState(); // Get SQLSTATE

                System.out.println("Code: " + sqlCode + "  sqlState: " + sqlState);
                System.out.println(e);
            }

            while(true) {
                System.out.print("\nPlease enter the date for appointment list [E] to exit: ");
                String date = sc.nextLine();
                if(date.equals("E"))
                    break ask_pid;

                while(true) {
                    String appointment_num = "";
                    String mname = "";
                    int hcardid = 0;
                    int pregnancy_num = 0;
                    int parent_id = 0;

                    // list all the appointment for that date
                    try {
                        String querySQL = "WITH A(pregnancy_num, parent_id, time) AS " +
                                "(SELECT pregnancy_num, parent_id, time FROM appointments" +
                                " WHERE practitioner_id = " + pid + " AND date = \'" + date +
                                "\'), B(pregnancy_num, parent_id, temp, time) AS " +
                                "(SELECT A.pregnancy_num, A.parent_id, (CASE WHEN primary_practitioner_id = " + pid + " THEN \'P\'" +
                                "WHEN backup_practitioner_id = " + pid + " THEN \'B\' END) as temp, time FROM pregnancies P, A" +
                                " WHERE A.pregnancy_num = P.pregnancy_num AND A.parent_id = P.parent_id" +
                                "), C(time, temp, hcardid, pregnancy_num, parent_id) AS " +
                                "(SELECT time, temp, hcardid, B.pregnancy_num, B.parent_id FROM B, parents P WHERE B.parent_id = P.parent_id)" +
                                " SELECT time, temp, mname, C.hcardid, pregnancy_num, parent_id FROM C, mothers WHERE mothers.hcardid = C.hcardid " +
                                "ORDER BY time";
    //                    System.out.println(querySQL);
                        ResultSet rs = statement.executeQuery(querySQL);

                        // check if there are appointments on that date
                        if (rs.next()) {
                            int i = 0;
                            rs.beforeFirst();
                            while (rs.next())
                            {
                                Time time = rs.getTime("time");
                                String temp = rs.getString("temp");
                                String name = rs.getString("mname");
                                int id = rs.getInt("hcardid");
                                System.out.print(++i + ": " + time + ", " + temp + ", " + name + " (" + id + ").\n");
                            }

                            System.out.print("\nEnter the appointment number that you would like to work on.\n" +
                                    "[E] to exit [D] to go back to another date: ");
                            appointment_num = sc.nextLine();
                            if (appointment_num.equals("E"))
                                break ask_pid;
                            if (appointment_num.equals("D"))
                                break;

                            rs = statement.executeQuery(querySQL);
                            rs.absolute(Integer.parseInt(appointment_num));
                            mname = rs.getString("mname");
                            hcardid = rs.getInt("hcardid");
                            pregnancy_num = rs.getInt("pregnancy_num");
                            parent_id = rs.getInt("parent_id");

                        } else {
                            System.out.println("There is no appointment on " + date);
                            continue;   // go back to ask date
                        }
                    } catch (SQLException e) {
                        sqlCode = e.getErrorCode(); // Get SQLCODE
                        sqlState = e.getSQLState(); // Get SQLSTATE

                        System.out.println("Code: " + sqlCode + "  sqlState: " + sqlState);
                        System.out.println(e);
                    }

                    options: while(true) {   // display previous menu with 5 options
                        System.out.println("\nFor " + mname + " (" + hcardid + ")");
                        System.out.println("1. Review notes\n" + "2. Review tests\n" + "3. Add a note\n" + "4. Prescribe a test\n" +
                                "5. Go back to previous menu\n");
                        System.out.print("Enter your choice: ");
                        int choice = sc.nextInt();
                        sc.nextLine();

                        switch (choice) {
                            // list all the notes that are relevant to this pregnancy by date time in descending order
                            case 1:
                                try {
                                    String querySQL = "SELECT date, time, content FROM notes " +
                                            "WHERE pregnancy_num = " + pregnancy_num + " AND parent_id = " + parent_id +
                                            " ORDER BY date DESC, time DESC";
                                    //                                System.out.println(querySQL);
                                    java.sql.ResultSet rs = statement.executeQuery(querySQL);

                                    while (rs.next()) {
                                        java.sql.Date d = rs.getDate("date");
                                        Time t = rs.getTime("time");
                                        String c = rs.getString("content");
                                        c = c.substring(0, Math.min(c.length(), 50));
                                        System.out.print(d + " " + t + " " + c + "\n");
                                    }
                                    break;
                                } catch (SQLException e) {
                                    sqlCode = e.getErrorCode(); // Get SQLCODE
                                    sqlState = e.getSQLState(); // Get SQLSTATE

                                    System.out.println("Code: " + sqlCode + "  sqlState: " + sqlState);
                                    System.out.println(e);
                                }

                                // list all the relevant tests (only for the mother) in descending order of date
                            case 2:
                                try {
                                    String querySQL = "SELECT prescribed_date, type, result FROM tests " +
                                            "WHERE pregnancy_num = " + pregnancy_num + " AND parent_id = " + parent_id +
                                            " AND baby_id IS NULL ORDER BY prescribed_date DESC";
                                    //                                System.out.println(querySQL);
                                    ResultSet rs = statement.executeQuery(querySQL);

                                    while (rs.next()) {
                                        java.sql.Date d = rs.getDate("prescribed_date");
                                        String t = rs.getString("type");
                                        String r = rs.getString("result");

                                        // if a result is unavailable then display PENDING
                                        if (r == null)
                                            r = "PENDING";
                                        else
                                            r = r.substring(0, Math.min(r.length(), 50));
                                        System.out.print(d + " [" + t + "] " + r + "\n");
                                    }
                                    break;
                                } catch (SQLException e) {
                                    sqlCode = e.getErrorCode(); // Get SQLCODE
                                    sqlState = e.getSQLState(); // Get SQLSTATE

                                    System.out.println("Code: " + sqlCode + "  sqlState: " + sqlState);
                                    System.out.println(e);
                                }

                                // let the user type in a text (note)
                            case 3:
                                System.out.print("Please type your observation: ");
                                String obs = sc.nextLine();
                                //                            sc.nextLine();
                                try {
                                    String querySQL = "SELECT MAX(n_id) FROM notes";
                                    ResultSet rs = statement.executeQuery(querySQL);
                                    rs.next();
                                    int nid = rs.getInt(1) + 1;

                                    java.util.Date d = new java.util.Date();
                                    SimpleDateFormat formatter = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
                                    String datetime = formatter.format(d);
                                    String[] str = datetime.split(" ");

                                    String insertSQL = "INSERT INTO notes(n_id, date, time, content, pregnancy_num, parent_id) VALUES " +
                                            "(" + nid + ", DATE \'" + str[0] + "\', TIME \'" + str[1] + "\', \'" + obs + "\', "
                                            + pregnancy_num + ", " + parent_id + ")";
                                    //                                System.out.println(insertSQL);
                                    statement.executeUpdate(insertSQL);
                                    break;
                                } catch (SQLException e) {
                                    sqlCode = e.getErrorCode(); // Get SQLCODE
                                    sqlState = e.getSQLState(); // Get SQLSTATE

                                    System.out.println("Code: " + sqlCode + "  sqlState: " + sqlState);
                                    System.out.println(e);
                                }

                                // let the user enter the type of test that is being prescribed
                            case 4:
                                System.out.print("Please enter the type of test: ");
                                String type = sc.nextLine();
                                try {
                                    // get maximum test id
                                    String querySQL = "SELECT MAX(test_id) FROM tests";
                                    //                                System.out.println(querySQL);
                                    ResultSet rs = statement.executeQuery(querySQL);
                                    rs.next();
                                    int tid = rs.getInt(1) + 1;

                                    // get current date
                                    java.util.Date d = new java.util.Date();
                                    SimpleDateFormat formatter = new SimpleDateFormat("yyyy-MM-dd");
                                    String prescribed_date = formatter.format(d);

                                    String insertSQL = "INSERT INTO tests(test_id, type, prescribed_date, sample_date, " +
                                            "pregnancy_num, parent_id, practitioner_id) VALUES (" +
                                            +tid + ", \'" + type + "\', DATE \'" + prescribed_date + "\', DATE \'" + prescribed_date + "\', " +
                                            pregnancy_num + ", " + parent_id + ", " + pid + ")";
                                    //                                System.out.println(insertSQL);
                                    statement.executeUpdate(insertSQL);
                                    break;
                                } catch (SQLException e) {
                                    sqlCode = e.getErrorCode(); // Get SQLCODE
                                    sqlState = e.getSQLState(); // Get SQLSTATE

                                    System.out.println("Code: " + sqlCode + "  sqlState: " + sqlState);
                                    System.out.println(e);
                                }
                            case 5:
                                break options;
                        }
                    }
                }
            }
        }
        System.out.println("EXIT");
        statement.close( );     // close all active connections
        con.close( );
        System.exit(0);
    }
}
