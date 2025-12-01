import mysql.connector

conn = mysql.connector.connect(
    host="srv577.hstgr.io",
    user="u164091347_admin",
    password="4aA#IZLN1",
    database="u164091347_db_master"
)

cursor = conn.cursor()

data = cursor.execute("SELECT * FROM customer")
rows = cursor.fetchall()
cols = cursor.column_names  # ได้ชื่อคอลัมน์อัตโนมัติ


print(cols)
print(rows)

cursor.close()
conn.close()