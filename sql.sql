CREATE TABLE client (
  clientId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL
);
CREATE TABLE project (
  projectId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  clientId INT NOT NULL,
  FOREIGN KEY (clientId) REFERENCES client(clientId)
);
CREATE TABLE workCode (
  workCodeId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  clientId INT NOT NULL,
  projectId INT NOT NULL,
  FOREIGN KEY (clientId) REFERENCES project(clientId),
  FOREIGN KEY (projectId) REFERENCES project(projectId)
);
CREATE TABLE activity (
  activityId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL
);
CREATE TABLE workCodeActivity (
  workCodeActivityId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  clientId INT NOT NULL,
  projectId INT NOT NULL,
  workCodeId INT NOT NULL,
  activityId INT NOT NULL,
  FOREIGN KEY (clientId) REFERENCES client(clientId),
  FOREIGN KEY (projectId) REFERENCES project(projectId),
  FOREIGN KEY (workCodeId) REFERENCES workCode(workCodeId),
  FOREIGN KEY (activityId) REFERENCES activity(activityId)
);
CREATE TABLE user (
  userId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email varchar(200) NOT NULL,
  firstname varchar(200) NOT NULL,
  surname varchar(200) NOT NULL,
  password varchar(400) NOT NULL
);