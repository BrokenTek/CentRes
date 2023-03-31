'''


Add Fourth Area Logic
Add Checks and Insertion blockers with graceful error messages

'''





import bottle
import mysql.connector

# Connect to database with same credentials
mydb = mysql.connector.connect(
    host="localhost",
    user="scott",
    password="tiger",
    database="centres"
)

# Open communication with database
# try:
mycursor = mydb.cursor()
# except mysql.connector.Error as e:
#     print("Error connecting to MySQL database: ", e)

class Increment:

    def __init__(self, increment_file='increments.txt'):
        self.increment_file = 'increments.txt'
        self.file = self.openFile()
        self.increment = self.readIncrement()

    def openFile(self):
        # Open increments.txt file
        try:
            return open(self.increment_file, 'r+')
        except FileNotFoundError as e:
            print("File not found: ", e, "Creating file...")
            newFile = open(self.increment_file, 'w+')
            newFile.write('0')
            bottle.redirect('/')
            return newFile


    def readIncrement(self):
        # Get increment value from increments.txt file
        file = self.openFile()
        return int(file.read())

    def writeIncrement(self, current_increment):
        # Update increments.txt file
        self.file.write(str(current_increment))

    def incrementValue(self):
        self.increment += 1
        # return self.increment

    def getIncrement(self):
        return self.increment

    def close_file(self):
        self.file.close()

    def __int__(self):
        return self.increment

    def __str__(self):
        return str(self.increment)

increment = Increment()

@bottle.route('/reset_menu_tables')
def reset_database():
    mycursor.execute("DELETE FROM menuassociations;")
    mycursor.execute("DELETE FROM menuitems;")
    mycursor.execute("DELETE FROM menucategories;")
    mycursor.execute("DELETE FROM menumodificationcategories;")
    mycursor.execute("INSERT INTO menucategories (quickCode, title) VALUES ('root', 'root');")
    mydb.commit()
    with open('increments.txt', 'w+') as f:
        f.write('0')

# Form template
@bottle.route('/')
def form_template():
    main_category = '''
        <h1>Main Category</h1>
        <form action="/first_area" method="post">
            <label for="title">Title:</label>
            <input type="text" name="title" /><br><br>
            <input type="submit" value="Submit" />
        </form>
    '''
    sub_category = '''
        <h1>Sub Category</h1>
        <form action="/second_area" method="post">
            <label for="title">Title:</label>
            <input type="text" name="title" /><br><br>
            <label for="root_category">Root Category:</label>
            <input type="text" name="root_category" /><br><br>
            <label for="route">Route To Kitchen:</label>
            <input type="checkbox" name="route" value="K"/><br><br>
            <input type="submit" value="Submit" />
        </form>
    '''

    # OLD MENU ITEM FORM
    # menu_item = '''
    #     <h1>Menu Item</h1>
    #     <form action="/third_area" method="post">
    #         <label for="title">Title:</label>
    #         <input type="text" name="title" />
    #         <label for="root_category">Root Category:</label>
    #         <input type="text" name="root_category" />
    #         <label for="sub_category">Sub Category:</label>
    #         <input type="text" name="sub_category" />
    #         <input type="submit" value="Submit" />
    #     </form>
    # '''

    menu_item = '''
    <h1>Menu Item</h1>
    <form action="/third_area" method="post">
        <label for="title">Title:</label>
        <input type="text" name="title"><br><br>
        <label for="category">Root Category:</label>
        <input type="text" name="category"><br><br>
        <label for="price">Price:</label>
        <input type="text" name="price"><br><br>
        <label for="route">Route To Kitchen:</label>
        <input type="checkbox" name="route" value="K"/><br><br>
        <input type="submit" value="Submit">
    </form>'''

    item_modification = '''
        <h1>Item Modification</h1>
        <form action="/fourth_area" method="post">
            <label for="title">Title:</label>
            <input type="text" name="title" /><br><br>
            <label for="root_category">Root Category:</label>
            <input type="text" name="root_category" /><br><br>
            <label for="sub_category">Sub Category:</label>
            <input type="text" name="sub_category" /><br><br>
            <label for="menu_item">Menu Item:</label>
            <input type="text" name="menu_item" /><br><br>
            <input type="submit" value="Submit" />
        </form>
    '''
    return main_category + sub_category + menu_item + item_modification


# First area form
@bottle.route('/first_area', method="POST")
def first_area():
    title = bottle.request.forms.get('title')
    quick_code = 'R' + str(increment.getIncrement()).zfill(3)

    sql = "INSERT INTO menucategories (quickCode, title) VALUES (%s, %s)"
    val = (quick_code, title)

    mycursor.execute(sql, val)

    sql = "INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (%s, %s)"
    val = ('root', quick_code)

    mycursor.execute(sql, val)

    mydb.commit()

    increment.incrementValue()

    bottle.redirect('/')

    increment.writeIncrement(increment.getIncrement())


# Second area form
@bottle.route('/second_area', method="POST")
def second_area():
    title = bottle.request.forms.get('title')
    root_category = bottle.request.forms.get('root_category')
    route = bottle.request.forms.get('route')

    # Get quickCode - possible error with comma
    sql = "SELECT quickCode FROM menucategories WHERE title = %s"
    val = (root_category,)
    mycursor.execute(sql, val)
    result = mycursor.fetchone()
    root_category = result[0]

    # title = bottle.request.forms.get('title')
    quick_code = 'S' + str(increment.getIncrement()).zfill(3)

    sql = "INSERT INTO menucategories (quickCode, title, route) VALUES (%s, %s, %s)"
    val = (quick_code, title, route)

    mycursor.execute(sql, val)

    sql = "INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (%s, %s)"
    val = (root_category, quick_code)

    mycursor.execute(sql, val)

    # Commit to database
    mydb.commit()

    increment.incrementValue()

    # Redirect to main template
    bottle.redirect('/')

    increment.writeIncrement(increment.getIncrement())


# Third area form
@bottle.route('/third_area', method="POST")
def third_area():
    title = bottle.request.forms.get('title')
    category = bottle.request.forms.get('category')
    price = bottle.request.forms.get('price')
    route = bottle.request.forms.get('route')

    sql = "SELECT quickCode FROM menucategories WHERE title = %s"
    val = (category,)
    mycursor.execute(sql, val)
    result = mycursor.fetchone()
    category = result[0]

    quick_code = 'I' + str(increment.getIncrement()).zfill(3)

    sql = "INSERT INTO menuitems (quickCode, title, price, route) VALUES (%s, %s, %s, %s)"
    val = (quick_code, title, price, route)

    mycursor.execute(sql, val)

    sql = "INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (%s, %s)"
    val = (category, quick_code)

    mycursor.execute(sql, val)

    # Commit to database
    mydb.commit()

    increment.incrementValue()

    # Redirect to main template
    bottle.redirect('/')

    increment.writeIncrement(increment.getIncrement())


# Fourth area form
@bottle.route('/fourth_area', method="POST")
def fourth_area():
    # Get form data
    title = bottle.request.forms.get('title')
    root_category = bottle.request.forms.get('root_category')
    sub_category = bottle.request.forms.get('sub_category')
    menu_item = bottle.request.forms.get('menu_item')
    quick_code = 'M' + str(increment.readIncrement()).zfill(2)

    sql = "INSERT INTO menucategories (quickCode, title) VALUES (%s, %s)"
    val = (quick_code, title)

    mycursor.execute(sql, val)

    sql = "INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (%s, %s)"
    val = (root_category, quick_code)

    mycursor.execute(sql, val)

    sql = "INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (%s, %s)"
    val = (sub_category, quick_code)

    mycursor.execute(sql, val)

    sql = "INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (%s, %s)"
    val = (menu_item, quick_code)

    mycursor.execute(sql, val)

    # Commit to database
    mydb.commit()

    increment.incrementValue()

    # Redirect to main template
    bottle.redirect('/')

    increment.writeIncrement(increment.getIncrement())

# increment.writeIncrement(increment.getIncrement())
increment.close_file()

# Run server
bottle.run(host='localhost', port=8080)
