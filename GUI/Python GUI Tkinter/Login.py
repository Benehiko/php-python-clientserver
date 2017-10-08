from tkinter import *
from core import Pyhandler

def getHomepage():
    from Homepage import runHomepage
    runHomepage()

def runLogin():
    login = Tk()

    def getUsernameandPassword():
        username = B.get()
        password = E.get()
        userpass = (username, password)
        ph = Pyhandler()
        if ph.login(username, password):
            print(userpass)
            getHomepage()
        return userpass

    A = Label(login, text="Enter Username:", font="system 20")
    B = Entry(login, bd=5)
    C = Button(login, text="Submit", font="system 20 bold", command=getUsernameandPassword)
    D = Label(login, text="Enter Password:", font="system 20")
    E = Entry(login, bd=5, show="*")


    A.grid(row=0, sticky="e")
    B.grid(row=0, column=1)
    D.grid(row=1, sticky="e")
    E.grid(row=1, column=1)
    C.grid(row=3, columnspan=2)


    login.mainloop()

