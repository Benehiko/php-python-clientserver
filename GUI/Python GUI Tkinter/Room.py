
class Room:

    def __init__(self):
        self.rooms = {}
        self.students = {}

    def addRoom(self, data):
        self.rooms.pop(data)
        return True

    def removeRoom(self, data):
        newarr = {}
        for x in self.rooms:
            if data["roomID"] != x["roomID"]:
                newarr.pop(x)
        self.rooms = newarr
        return True

    def addUser(self, data):
        roomID = data["roomID"]
        students = data["students"]
        for x in self.rooms:
            if roomID == x["roomID"]:
                for y in x["roomID"]["students"]:
                    for student in y["userID"]:
                        if students["userID"] != student:
                            x["students"].append

        return False

    def sync(self, data):
        self.rooms = data


