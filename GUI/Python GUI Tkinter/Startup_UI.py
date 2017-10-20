from tkinter import *
#from core import Pyhandler
from tkinter import messagebox
from gui import Gui


top = Tk()

top.title("EG's group marking application")
def getRegister():
    gui.register()

def getLogin():
    gui.login()

def showHelp():
    messagebox.showinfo("Help", "If you are a new user, click on Register to create an account. If you already have an account, click on Log in.")

A = Button(top, text="?", command=showHelp, font="comic 15 bold", fg="blue")
B = Button(top, text="Register", font="system 40 bold", command=getRegister)
C = Button(top, text="Log In", font="system 40 bold", command=getLogin)

A.grid(row=0)
B.grid(row=1)
C.grid(row=2)

top.mainloop()
