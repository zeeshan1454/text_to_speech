<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audio Player</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .audio-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .audio-controls button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 24px;
        }

        .audio-length {
            margin-left: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<table>
    <tr>
        <td>
            <div class="audio-controls">
                <button class="play-toggle" data-file="ivr_file/2024-06-08_13-39-02.wav">
                    <i class="fas fa-play"></i>
                </button>
                <a href="ivr_file/2024-06-08_13-39-02.wav" download>
                    <i class="fas fa-download"></i>
                </a>
                <span class="audio-length" id="audio-length-2024-06-08_13-39-02.wav"></span>
            </div>
        </td>
    </tr>
</table>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const audioControls = document.querySelectorAll(".audio-controls");

        audioControls.forEach(control => {
            const playToggle = control.querySelector(".play-toggle");
            const audioFile = playToggle.getAttribute("data-file");
            const audioLengthSpan = control.querySelector(".audio-length");

            const audio = new Audio(audioFile);
            let isPlaying = false;

            audio.addEventListener("loadedmetadata", () => {
                const minutes = Math.floor(audio.duration / 60);
                const seconds = Math.floor(audio.duration % 60);
                audioLengthSpan.textContent = `${minutes}:${seconds.toString().padStart(2, "0")}`;
            });

            playToggle.addEventListener("click", () => {
                if (isPlaying) {
                    audio.pause();
                    playToggle.innerHTML = '<i class="fas fa-play"></i>';
                } else {
                    audio.play();
                    playToggle.innerHTML = '<i class="fas fa-pause"></i>';
                }
                isPlaying = !isPlaying;
            });

            audio.addEventListener("ended", () => {
                isPlaying = false;
                playToggle.innerHTML = '<i class="fas fa-play"></i>';
            });
        });
    });
</script>

</body>
</html>
