@echo off

set /p input="Please enter the input video path (including file name suffix): "
if not exist "%input%" (
    echo The video file does not exist, please re-enter!
    goto start
)

set /p output1="Please enter the output path:"
set /p output="Please enter the output file name (without the .mpd suffix):"

md %output1%

"./ffmpeg.exe" -y -i "%input%" -c:v libx264 -c:a aac -bf 1 -keyint_min 25 -g 250 -sc_threshold 40 -use_timeline 1 -use_template 1 -seg_duration 15 -hls_playlist 0 -f dash -map 0 -s:v:0 256x144 -b:v:0 95k -map 0 -s:v:1 426x240 -b:v:1 150k -map 0 -s:v:2 640x360 -b:v:2 276k -map 0 -s:v:3 854x480 -b:v:3 750k -map 0 -s:v:4 1280x720 -b:v:4 2048k -map 0 -s:v:5 1920x1080 -b:v:5 4096k -strict -2 -threads 12 "%output%.mpd"

echo "Processing completed!"
pause
