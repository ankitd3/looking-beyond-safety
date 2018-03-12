import matplotlib.pyplot as plt
import matplotlib.image as mpimg
import numpy as np
import cv2
import math
import os
import time
from moviepy.editor import VideoFileClip

def grayscale(img):
    """Applies the Grayscale transform
    This will return an image with only one color channel
    but NOTE: to see the returned image as grayscale
    (assuming your grayscaled image is called 'gray')
    you should call plt.imshow(gray, cmap='gray')"""
    return cv2.cvtColor(img, cv2.COLOR_RGB2GRAY)
    # Or use BGR2GRAY if you read an image with cv2.imread()
    # return cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

def canny(img, low_threshold, high_threshold):
    """Applies the Canny transform"""
    return cv2.Canny(img, low_threshold, high_threshold)

def gaussian_blur(img, kernel_size):
    """Applies a Gaussian Noise kernel"""
    return cv2.GaussianBlur(img, (kernel_size, kernel_size), 0)

def region_of_interest(img, vertices):
    """
    Applies an image mask.

    Only keeps the region of the image defined by the polygon
    formed from `vertices`. The rest of the image is set to black.
    """
    #defining a blank mask to start with
    mask = np.zeros_like(img)

    #defining a 3 channel or 1 channel color to fill the mask with depending on the input image
    if len(img.shape) > 2:
        channel_count = img.shape[2]  # i.e. 3 or 4 depending on your image
        ignore_mask_color = (0,255,) * channel_count
    else:
        ignore_mask_color = 255

    #filling pixels inside the polygon defined by "vertices" with the fill color
    cv2.fillPoly(mask, vertices, ignore_mask_color)

    #returning the image only where mask pixels are nonzero
    masked_image = cv2.bitwise_and(img, mask)
    #plt.imshow(masked_image)
    #plt.show()
    return masked_image


def draw_lines(img, lines, color=[255, 0, 0], thickness=2):
    for line in lines:
        for x1,y1,x2,y2 in line:
            cv2.line(img, (x1, y1), (x2, y2), color, thickness)
    """
    NOTE: this is the function you might want to use as a starting point once you want to
    average/extrapolate the line segments you detect to map out the full
    extent of the lane (going from the result shown in raw-lines-example.mp4
    to that shown in P1_example.mp4).

    Think about things like separating line segments by their
    slope ((y2-y1)/(x2-x1)) to decide which segments are part of the left
    line vs. the right line.  Then, you can average the position of each of
    the lines and extrapolate to the top and bottom of the lane.

    This function draws `lines` with `color` and `thickness`.
    Lines are drawn on the image inplace (mutates the image).
    If you want to make the lines semi-transparent, think about combining
    this function with the weighted_img() function below
    """





    #for line in lines:
    #    x1=line[0]
    #    y1=line[1]
    #    x2=line[2]
    #    y2=line[3]
    #    cv2.line(img, (x1, y1), (x2, y2), color, thickness)


def hough_lines(img, rho, theta, threshold, min_line_len, max_line_gap):
    """
    `img` should be the output of a Canny transform.

    Returns an image with hough lines drawn.
    """
    lines = cv2.HoughLinesP(img, rho, theta, threshold, np.array([]), minLineLength=min_line_len, maxLineGap=max_line_gap)
    line_img = np.zeros((img.shape[0], img.shape[1], 3), dtype=np.uint8)
    #draw_lines(line_img, lines)
    #print(len(lines))
    #lines_avg=average_slope_intercept(lines,img)
    #print(lines_avg)
    draw_lines(line_img,lines)
    #plt.imshow(line_img)
    #plt.show()

    magic(lines,img)
    #writes the lane number to a file l.txt
    return line_img

def magic(lines,img):
    #print(lines)
    c_l=img.shape[1]*0.4
    c_r=img.shape[1]*0.6
    x=[]
    l,r=[0,0]
    for line in lines:
        x.append(int(line[0][0]))
    #print(x)
    for i in x:
        if(i<c_l):
            l+=1
        elif(i>c_r):
            r+=1
    if(r==0):
        lane_number=3
    elif(l==0):
        lane_number=1
    else:
        lane_number=2
    file_path="l.txt"
    fow=open(file_path,'w')
    fow.write(str(lane_number))
    fow.close()

def weighted_img(img, initial_img, α=0.8, β=1., λ=0.):
    """
    `img` is the output of the hough_lines(), An image with lines drawn on it.
    Should be a blank image (all black) with lines drawn on it.

    `initial_img` should be the image before any processing.

    The result image is computed as follows:

    initial_img * α + img * β + λ
    NOTE: initial_img and img must be the same shape!
    """
    return cv2.addWeighted(initial_img, α, img, β, λ)

def process_frame(image):
    global first_frame

    gray_image = grayscale(image)
    img_hsv = cv2.cvtColor(image, cv2.COLOR_RGB2HSV)
    #plt.imshow(img_hsv)
    #plt.show()
    #hsv = [hue, saturation, value]
    #more accurate range for yellow since it is not strictly black, white, r, g, or b

    lower_yellow = np.array([20, 100, 100], dtype = "uint8")
    upper_yellow = np.array([30, 255, 255], dtype="uint8")

    mask_yellow = cv2.inRange(img_hsv, lower_yellow, upper_yellow)
    mask_white = cv2.inRange(gray_image, 200, 255)
    mask_yw = cv2.bitwise_or(mask_white, mask_yellow)
    mask_yw_image = cv2.bitwise_and(gray_image, mask_yw)

    kernel_size = 5
    gauss_gray = gaussian_blur(mask_yw_image,kernel_size)



    #same as quiz values
    low_threshold = 50
    high_threshold = 150
    canny_edges = canny(gauss_gray,low_threshold,high_threshold)
    #plt.imshow(canny_edges)
    #plt.show()

    imshape = image.shape
    lower_left = [0,imshape[0]]
    lower_right = [imshape[1],imshape[0]]
    top_left = [0,imshape[0]*0.55]
    top_right=[imshape[1],imshape[0]*0.55]
    top_left_i = [imshape[1]*0.3,imshape[0]*0.6]
    top_right_i = [imshape[1]*0.7,imshape[0]*0.6]
    vertices = [np.array([lower_left,top_left,top_right,lower_right,top_right_i,top_left_i],dtype=np.int32)]
    #print(vertices)
    #print(imshape)
    roi_image = region_of_interest(canny_edges, vertices)
    #plt.imshow(roi_image)
    #plt.show()

    #rho and theta are the distance and angular resolution of the grid in Hough space
    #same values as quiz
    rho = 2
    theta = np.pi/180
    #threshold is minimum number of intersections in a grid for candidate line to go to output
    threshold = 20
    min_line_len = 50
    max_line_gap = 60

    line_image = hough_lines(roi_image, rho, theta, threshold, min_line_len, max_line_gap)
    #plt.imshow(line_image)
    #plt.show()

    result = weighted_img(line_image, image, α=0.8, β=1., λ=0.)
    return result

#for source_img in os.listdir("test_images/"):

for source_img in ['frame0.jpg','frame200.jpg','frame400.jpg','frame600.jpg','frame800.jpg','frame1000.jpg','frame1200.jpg','frame1400.jpg']:
    image = mpimg.imread("test_images/"+source_img)
    processed = process_frame(image)
    mpimg.imsave("out_images/"+source_img,processed)
    #time.sleep(8)
