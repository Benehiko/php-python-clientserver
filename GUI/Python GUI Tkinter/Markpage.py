from tkinter import *
from tkinter import messagebox
#from PIL import Image, ImageTk


def runMarkpage():

    markpage = Tk()
    markpage.title("Homepage")
    markpage.geometry("1000x800")

    def commentsubmitted():
        comment = g.get()
        j.insert(0, comment)
        messagebox.showinfo("Comment", "Your comment has been submitted")


    # background_image = Image.open('EG_Logo.jpg')
    # background_image = background_image.resize((300, 250))
    # photo = ImageTk.PhotoImage(background_image)
    # z = Label(markpage, image=photo)
    # z.place(x=10, y=10)
    # z.image = background_image


    frame1 = Frame(markpage)
    frame1.pack()
    frame2 = Frame(markpage)
    frame2.pack(ipady=20)
    frame3 = Frame(markpage)
    frame3.pack(ipady=20)
    frame4 = Frame(markpage)
    frame4.pack()



    a = Label(frame1, text="R8ME", font="device 30 bold", height=2, fg="blue")
    b = Label(frame1, text="Admin name", font="device 15")
    d = Label(frame2, text="Students:", bd=20, font="ansi 20")
    e = Label(frame3, text="Add a comment:", font="ansi 20")
    f = Label(frame4, text="Comments:", font="ansi 20")
    l = Button(frame3, text="Submit", font="system 20 bold", command=commentsubmitted)
    g = Entry(frame3, bd=3)

    i = Listbox(frame2, bd=2)
    i.insert(0, "Student 1")
    i.insert(1, "Student 2")
    j = Listbox(frame4, bd=2)
    k = Scrollbar(j)

    m = Button(frame2, text="Enter", font="system 10 bold")
    m.pack(side=RIGHT, padx=10)
    h = Entry(frame2, bd=3, width=5)
    h.pack(side=RIGHT)
    x = Label(frame2, text="Individual marks:", font="ansi 15")
    x.pack(side=RIGHT, padx=10)
    y = Label(frame2, text="Total Mark:", font="device 20 bold")
    y.pack(side=BOTTOM)

    a.pack()
    b.pack()
    #C.pack(side=RIGHT)
    d.pack(side=LEFT)
    e.pack(side=LEFT)
    f.pack()
    l.pack(side=RIGHT, padx=20)

    g.pack(side=LEFT)
    i.pack(side=RIGHT, ipadx=50)
    j.pack(ipadx=150, ipady=100)
    k.pack(side=RIGHT, fill=Y)


    markpage.mainloop()


runMarkpage()