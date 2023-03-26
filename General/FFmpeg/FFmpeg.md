sample_v1 - 320x240
sample_v2 - 480x270


C:\Users\iman\Desktop\Video


ffmpeg -i sample_v1.mp4 -vf "drawtext=text='Centered Text':x=(w-text_w)/2:y=(h-text_h)/2:fontsize=24:fontcolor=white" -c:a copy  sample_v1_output.mp4

ffmpeg -i sample_v1.mp4 -vf "drawtext=fontfile= /Windows/fonts/calibri.ttf:fontsize=18: fontcolor=black:x=(w-text_w)/2:y=(h-text_h)/2:text=word" output.mp4


Resize:
ffmpeg -i sample_v1.mp4 -s 160x140 -c:a copy sample_v1_180_140.mp4

Generate thumbnail:
ffmpeg -i sample_v1.mp4 -ss 00:00:01.000 -vframes 1 thumb.png
ffmpeg -i sample_v1.mp4 -vf "thumbnail" -frames:v 1 thumbnail.png
ffmpeg -i sample_v1.mp4 -vf thumbnail,scale=300:200 -frames:v 1 out.png

Text on video:
ffmpeg -i sample_v1.mp4 -vf "drawtext=fontfile= /Windows/fonts/calibri.ttf:fontsize=18: fontcolor=green:x=(w-text_w)/2:y=(h-text_h)/2:text='Hello RGS'" sample_v1_output.mp4

ffmpeg -i image.png -vf "drawtext=fontfile= /Windows/fonts/calibri.ttf:fontsize=18: fontcolor=green:x=(w-text_w)/2:y=(h-text_h)/2:text='Hello RGS'" image_output.png


ffmpeg -i S221231LA002.mp4 -vf "drawtext=fontfile=/Windows/fonts/calibri.ttf:text='Welcome to webmast':fontcolor=white:fontsize=24:alpha='if(lt(mod(t,10),5),1,0)'" -codec:a copy S221231LA002_out.mp4

ffmpeg -i S221231LA002.mp4 -vf "drawtext=fontfile=/Windows/fonts/calibri.ttf:text='Welcome to webmast':fontsize=30:fontcolor=black:x=w-tw-100:y=100:box=1:boxcolor=white@0.4:boxborderw=5:alpha='if(lt(mod(t,10),5),1,0)" -codec:a copy S221231LA002_out.mp4





add sound to video:
ffmpeg -i image_to_video_8.mp4 -i sound.mp3 -c copy -map 0:v:0 -map 1:a:0 image_to_video_8_out.mp4

Concate video:
ffmpeg -f concat -safe 0 -i video.txt -c copy  output.mp4
ffmpeg -f concat -safe 0 -i video.txt -vf "settb=AVTB,setpts=N/23.98/TB,fps=23.98" output3.mp4
ffmpeg -f concat -safe 0 -i video.txt -vf "fps=23.98" output.mp4
https://superuser.com/questions/1671523/ffmpeg-concat-input-txt-set-frame-rate

Image to Video:
Image to video with frame rate:
ffmpeg -framerate 1 -i image_processed_8.png -c:v libx264 -vf "pad=ceil(iw/2)*2:ceil(ih/2)*2" -r 23.98 -pix_fmt yuv420p image_to_video_test.mp4


Get Frame rate:
ffprobe -v 0 -of compact=p=0 -select_streams 0 -show_entries stream=r_frame_rate main_video_with_name_8.mp4
ffprobe -v 0 -of compact=p=0 -select_streams 0 -show_entries stream=r_frame_rate image_to_video_8.mp4

r_frame_rate=24000/1001

https://stackoverflow.com/questions/37395576/ffmpeg-how-to-determine-frame-rate-automatically




ffmpeg -i input.mp4 -i image_processed_8.png -filter_complex "overlay=(main_w-overlay_w)/2:(main_h-overlay_h)/2:enable='between(t,0,5)'" output2.mp4

ffmpeg -i input.mp4 -i name_image_8.png -filter_complex "overlay=W-w:70:enable='if(lt(mod(t,10),5),1,0)'" output_periodic2.mp4
ffmpeg -i input.mp4 -i name_image_8.png -filter_complex "overlay=W-w:70:enable='if(lt(mod(t,10),5),1,0)'" output_periodic2.mp4


ffmpeg -i "image_to_video_test.mp4" -f lavfi -i aevalsrc=0 -shortest -y "image_to_video_8_new.mp4"
https://superuser.com/questions/1096921/concatenating-videos-with-ffmpeg-produces-silent-video-when-the-first-video-has


https://video.stackexchange.com/questions/12105/add-an-image-overlay-in-front-of-video-using-ffmpeg
addNamBannerToVideo
ffmpeg -i C:/BM/Video/GradClip_Demo_USC.mp4 -i C:/BM/Video/name_image.png -filter_complex "overlay=W-w:0:enable='if(lt(mod(t,10),5),1,0)'" C:/BM/Video/GradClip_Demo_USC_out.mp4 2> C:/BM/Video/addNamBannerToVideo.txt

ffmpeg -i C:/BM/Video/big_video.mp4 -i C:/BM/Video/name_image.png -filter_complex "[0:v][1:v] overlay=W-w:0:enable='if(lt(mod(t,10),5),1,0)'" -pix_fmt yuv420p -c:a copy C:/BM/Video/big_video_out_final.mp4

ffmpeg -i C:/BM/Video/big_video.mp4 -i C:/BM/Video/name_image.png -filter_complex "[0:v][1:v] overlay=W-w:0:enable='if(lt(mod(t,10),5),1,0)'" C:/BM/Video/big_video_out_final2.mp4
ffmpeg -i C:/BM/Video/GradClip_Demo_USC.mp4 -i C:/BM/Video/name_image.png -filter_complex "[0:v][1:v] overlay=W-w:0:enable='if(lt(mod(t,10),5),1,0)'" C:/BM/Video/GradClip_Demo_USC_out.mp4

Cut vide:
ffmpeg -ss 00:00:00 -to 00:00:2 -i input.mp4 -acodec copy -vcodec copy output.mp4

Mute video:
ffmpeg -i input.mp4 -af "volume=enable='between(t,5,10)':volume=0, volume=enable='between(t,15,20)':volume=0" output.mp4

New Command:
ffmpeg -i C:/BM/Video/GradClip_Demo_USC.mp4 -i C:/BM/Video/name_image.png -filter_complex "[0:v][1:v] overlay=25:25:enable='between(t,0,20)'" -pix_fmt yuv420p -c:a copy C:/BM/Video/GradClip_Demo_USC_out.mp4
ffmpeg -i C:/BM/Video/big_video.mp4 -i C:/BM/Video/name_image.png -filter_complex "[0:v][1:v] overlay=25:25:enable='between(t,0,20)'" -pix_fmt yuv420p -c:a copy C:/BM/Video/big_video_out.mp4
