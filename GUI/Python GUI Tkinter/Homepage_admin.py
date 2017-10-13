from tkinter import *
from tkinter import messagebox
#from PIL import Image, ImageTk

def getMarkpage():
    from Markpage import runMarkpage
    runMarkpage()


def runHomepage():
    homepage = Tk()
    homepage.title("Homepage")
    homepage.geometry("1000x800")

    def showgroup(event):
        selection = str(event)
        print(selection)

    # background_image = Image.open('EG_Logo.jpg')
    # background_image = background_image.resize((300, 250))
    # photo = ImageTk.PhotoImage(background_image)
    # z = Label(homepage, image=photo)
    # z.place(x=10, y=10)
    # z.image = background_image


    frame1 = Frame(homepage)
    frame1.pack()
    frame2 = Frame(homepage)
    frame2.pack()
    frame3 = Frame(homepage)
    frame3.pack(ipady=0)
    frame4 = Frame(homepage)
    frame4.pack()



    a = Label(frame1, text="R8ME", font="device 30 bold", height=2, fg="blue")
    b = Label(frame1, text="Admin name", font="device 15")
    d = Label(frame2, text="Available groups:", bd=20, font="ansi 20")

    h = Button(frame2, text="Mark", font="system 20 bold", command=getMarkpage, fg="red")
    i = Listbox(frame2, bd=2, selectmode=SINGLE, font="ansi 15")
    i.insert(0, "Group 1")
    i.insert(1, "Group 2")
    i.bind("<Button-1>", showgroup)

    # e = Label(frame3, text="Selected group:", font="ansi 20")
    # e.pack(side=LEFT)


    a.pack()
    b.pack()
    d.pack(side=LEFT)
    h.pack(side=RIGHT, padx=20)
    i.pack(side=RIGHT, ipadx=50, pady=40)




    homepage.mainloop()


runHomepage()