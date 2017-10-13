from tkinter import *
from tkinter import messagebox
#from PIL import Image, ImageTk



def runHomepage():
    homepage = Tk()
    homepage.title("Homepage")
    homepage.geometry("1000x800")

    def commentsubmitted():
        comment = g.get()
        j.insert(0, comment)
        messagebox.showinfo("Comment", "Your comment has been submitted")

    # background_image = Image.open('EG_Logo.jpg')
    # background_image = background_image.resize((300, 250))
    # photo = ImageTk.PhotoImage(background_image)
    # z = Label(homepage, image=photo)
    # z.place(x=10, y=10)
    # z.image = background_image

    y = Label(homepage, text="Total Mark:", font="device 30 bold")
    y.place(x=50, y=500)

    frame1 = Frame(homepage)
    frame1.pack()
    frame2 = Frame(homepage)
    frame2.pack()
    frame3 = Frame(homepage)
    frame3.pack(ipady=20)
    frame4 = Frame(homepage)
    frame4.pack()



    a = Label(frame1, text="R8ME", font="device 30 bold", height=2)
    b = Label(frame1, text="Admin name", font="device 15")
    d = Label(frame2, text="Your group members:", bd=20, font="ansi 20")
    e = Label(frame3, text="Add a comment:", font="ansi 20")
    f = Label(frame4, text="Comments:", font ="ansi 20")

    g = Entry(frame3, bd=3)
    h = Button(frame3, text="Submit", font="system 20 bold", command=commentsubmitted)
    i = Listbox(frame2, bd=2)
    j = Listbox(frame4, bd=2, font="ansi 15")
    k = Scrollbar(j)


    a.pack()
    b.pack()
    d.pack()
    e.pack(side=LEFT)
    f.pack()

    h.pack(side=RIGHT, padx=20)
    g.pack(side=LEFT)
    i.pack(side=BOTTOM, ipadx=50)
    j.pack(ipadx=150, ipady=100)
    k.pack(side=RIGHT, fill=Y)


    homepage.mainloop()


runHomepage()