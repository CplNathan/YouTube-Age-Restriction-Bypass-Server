 <?php
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');

    require __DIR__ . '/vendor/autoload.php';

    use YoutubeDl\Options;
    use YoutubeDl\YoutubeDl;

    $yt = new YoutubeDl();
    $yt->setBinPath("/usr/local/bin/youtube-dl"); // This may need changing

    $dlvid = escapeshellcmd($_GET['id']);

    $dlfilename = $dlvid . ".mp4";

    $dlreldir = '/dl/' . $dlvid;

    $dlurl = 'https://' . $_SERVER['HTTP_HOST'] . $dlreldir . '/' . $dlfilename;

    try {
        if (is_file(__dir__ . $dlreldir . '/' . $dlfilename)) {
            echo json_encode(array(
                'url' => $dlurl
            ));
        } else {
            $collection = $yt->download(
                Options::create()
                    ->downloadPath(__dir__ . $dlreldir)
                    ->url('https://www.youtube.com/watch?v=' . $dlvid)
                    ->output($dlfilename)
            );

            foreach ($collection->getVideos() as $video) {
                if ($video->getError() !== null) {
                    echo json_encode(array(
                        'url' => $dlurl,
                        'error' => $video->getError()
                    ));
                } else {
                    echo json_encode(array(
                        'url' => $dlurl
                    ));
                }
            }
        }
    } catch (RuntimeException  $e) {
        echo json_encode(array(
            'url' => $dlurl,
            'error' => $e
        ));
    }
