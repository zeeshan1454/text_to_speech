<!doctype html>
<html lang="en">
  <head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>
  <audio class="audio-player" id="myTune">
<source src="http://10.10.10.47/RECORDING/6272208/RECORDING/9651784939_6272208_2023-11-27-16:56:00_6395630844.wav" type="audio/wav">
</audio>
<button class="control btn btn-sm btn-info font-weight-bold" type="button" onclick="aud_play_pause(this.previousElementSibling)">
            <span id="play-pause-icon">▶</span>
        </button>
        <a href="http://10.10.10.47/RECORDING/6272208/RECORDING/9651784939_6272208_2023-11-27-16:56:00_6395630844.wav" download>
        <button class="download-button control btn btn-sm btn-info font-weight-bold" type="button">
            <i class="bi bi-arrow-down-square"></i>
        </button>
    </a>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
        var currentAudio = null;

        function aud_play_pause(audio) {
            if (currentAudio !== audio) {
                if (currentAudio) {
                    currentAudio.pause();
                    var currentPlayPauseIcon = currentAudio.nextElementSibling.querySelector('.control span');
                    currentPlayPauseIcon.textContent = '▶'; // Reset the icon
                }
                currentAudio = audio;
            }

            if (audio.paused) {
                audio.play();
                var playPauseIcon = audio.nextElementSibling.querySelector('.control span');
                playPauseIcon.textContent = '⏸';
            } else {
                audio.pause();
                var playPauseIcon = audio.nextElementSibling.querySelector('.control span');
                playPauseIcon.textContent = '▶';
            }
        }
    </script>
  </body>
</html>