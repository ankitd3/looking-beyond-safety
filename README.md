# looking-beyond-safety
Lane detection to find lane number for dynamic speed limit
Project Design
GUI:
In our system, we consider that a camera has been placed at a considerable height in every car. This camera is responsible for relaying a live feed to the central servers. 
In our GUI, the location of every car can be seen via google maps and the speed limit for each area is predetermined to a certain extent. The speed of the car is considered to have been relayed from the digital speedometer in the car. This is visible in the bar at the bottom of the screen.

Process:
The python file contains all the functions that are needed for image processing(The process has been explained later.) 
The frames (images) are extracted from the video input (feed from the car’s video camera).
There is a folder “input_img” into which all the frames are updated every three seconds.
We have considered a scenario of a three lanes for our project.
The speed limit has been set as 30 kmph for the city lanes and for the highways, the speed limit will be set depending upon the lane detected for each lane. (say 65 for fastest, 60 for medium and 55 for the slowest).
If a vehicle tries to overtake from the slower lane, as the speed limit value is lesser, an overspeeding will be recorded.
Everytime an image is selected, processed and the lane_number is identified, the output image is stored to the folder “out_images” with the name “1.jpg” so that our web page correctly presents the current output image and its inference at regular intervals.

Region of interest :
   
lower_left = [0,imshape[0]]
lower_right = [imshape[1],imshape[0]]
top_left = [0,imshape[0]*0.55]
top_right=[imshape[1],imshape[0]*0.55]
top_left_i = [imshape[1]*0.3,imshape[0]*0.6]
top_right_i = [imshape[1]*0.7,imshape[0]*0.6]
c_l=img.shape[1]*0.4
c_r=img.shape[1]*0.6


Algorithm:
For all the 1st person images, the lane in which the vehicle is driven upon will always be in  the center of the image.
We are not interested in our own lane, but the count of other lanes in the left and right area to find the lane number.
Hence, the quadrilateral : lower_left → top_left_i → top_right_i → lower_right defines the area in which there will be canny edges of our own lane.
All the image except this may contain the other desired lanes along with unwanted objects such as clouds, white coloured stop signs, etc which may give false line segments.
To avoid this, we eliminate the upper quadrilateral too : (0,0) → (0,imshape[1]) → top_right → top_left.
After eliminating these two regions, we get our desired region of interest, a concave hexagon : lower_left → top_left_i → top_right_i → lower_right → top_right → top_left.
This region of image gives us all the desired lanes on the road.
We use two variables in our algorithm for classification first, “c_l” and the other “c_r”.
We elicit the x-coordinates of the line segments drawn for the lanes in the region of interest and compare each of them with “c_l” and “c_r”.
If the x-coordinates are less than c_l, that means the lane(line segment) must be a slower lane and we increase the count of left_lanes by 1. And if the x-coordinates are greater than c_r, that means the lane(line segment) discovered must be a faster lane, the count of right_lanes is incremented.
Hence, in a 3 lane road, the value of lane_number is determined by :
If right_lanes count is 0, lane_number must be 3 (fastest), as there will be 2 lanes detected in the left part of the image.
Similarly if the count of left_lanes is 0, we must be in the 1st lane (slowest).
And if both the count of left_lanes and right_lanes is greater than 0, we infer that we are in the middle lane (lane_number=2).
Now, this lane_number is written to a file “l.txt” to let our webpage read it.


Project Test Case Report:
Test case 1:
Initialisation : 
location : (19.019474137261, 73.01705417305)
speed : 0 kmph
These location coordinates are passed to our database to check in which region does our vehicle belongs, either 
Area 1: Inner city roads, single lane and a speed limit of 30kmph 
Area 2: Palm Beach Highway, Navi Mumbai, 3 lane road with a speed limit of 55 - 60 - 65 kmph for the three lanes.
Area 3: DPS, Navi Mumbai school road, single lane with speed limit of 15 kmph.

The initial coordinates belong to the first area with a speed limit of 30 kmph, which is then displayed in the component 1 in the GUI mentioned below.
Components 4,5 and 6 are not displayed for Area 1.
If the current speed is increased by using the speed bar (7th Component) over 20 kmph (30-10), component 1 displays a warning and for the speed over 30 kmph, Overspeeding is recorded and the details of the driver are stored in the offenders’ table.
The overspeeding is recorded every time the location of the vehicle changes.
Test Case 2:
Now, if the marker is moved to the coordinates : (19.021239031,73.00832090) -> palm beach road.
The coordinates now belong to Area 2, the php file will run the shell script which contains the execution command for the python file :   
The python file takes a video as input and extracts the frames from the video at regular intervals. 
These frames are then processed for lane detection and the driver’s lane is identified accurately.
This lane number is stored into the “l.txt” file so that our web page can extract it and inference is drawn.
The speed limit is updated every time the driver changes its lane and the overspeeding is recorded every time the vehicle goes beyond the dynamic speed limit set.

GUI and Results
Components:
Speed limit / warning / Overspeeding display
Location coordinates
Map with a marker (car)
Lane number
Processed image output
Inferenced diagram
Current speed bar




OUTPUT: These images that are obtained after every step of lane detection

1)	Raw input image:


2)	cv2.cvtColor(img, cv2.COLOR_RGB2GRAY):

3)	cv2.cvtColor(gray_image, cv2.COLOR_RGB2HSV):

4)	cv2.GaussianBlur:
5)	low_th = 50 high_th = 150
cv2.canny(gauss_gray,low_th,high_th):

6)	region_of_interest(canny_edges, vertices):

cv2.HoughLinesP (returns line segments to be drawn)  → cv2.line (draws the lines on the original image)



Output Image: The lanes other than the our own lane is identified and marked with red line segments.

In this case, lane number = 2, and “2” will be stored in the “l.txt” file.

Conclusions
Our project, “Looking Beyond Safety” aims to ensure that road safety is improved by the means of the dynamic speed limit system in a cost effective manner. 
We were successfully able to derive the frames from the video feed. This video feed was provided by a camera that was fixed the car.
The frames were processed and we were able to detect the lanes accurately using OpenCV in maximum cases(ie ideal cases).
Our GUI consisted of the google maps API which helped us to identify whether the car was in an inside lane or a highway, depending on which, we were able to assign a speed limit for each lane of the road distinctively.
If the driver’s speed is close to the assigned limit, he gets a warning. 
The location, speed and the timestamp was automatically logged in our database if the current speed bar exceeded the assigned speed limit.
Future Scope
The project can be extended further to find out the lanes for multiple cars in an efficient manner.
The method used to find the lanes can be improved to handle exceptions such as congested roads.
The system should be able to work even in cases of flyovers. This can be done by using the altitude or by image detection techniques. 
The camera can be used to detect potholes. This information can then be sent to the concerned authorities.  
