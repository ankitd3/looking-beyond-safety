import cv2
vidcap = cv2.VideoCapture('bucks.mp4')
success,image = vidcap.read()
count = 0
success = True
while success:
	success,image = vidcap.read()
	#print('Read a new frame: ', success)
	if(count%200==0):
		cv2.imwrite("test_images/frame%d.jpg" % count, image)     # save frame as JPEG file
	count += 1