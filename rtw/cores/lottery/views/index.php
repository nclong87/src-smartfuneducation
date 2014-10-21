<div style="display: block;">
    <div id="left_column" style="text-align: center">
        <h2>Bạn đang có <font color="orange"><?php echo number_format($player_info->lottery_turn) ?></font> lượt quay số</h2>
        <center><div id="lottery_container"></div></center>
        <br clear="all"/>
    </div>
    <div id="right_column">
        <?php echo $widget_player_info?>
    </div>
</div>
<script src="/mod/rtw/js/kinetic-v5.0.1.min.js"></script>
<script defer="defer">
    //constants
    var MAX_ANGULAR_VELOCITY = 360 * 5;
    var NUM_WEDGES = 10;
    var WHEEL_RADIUS = 200;
    var ANGULAR_FRICTION = 0.2;

    // globals
    var angularVelocity = 0;
    var lastRotation = 1;
    var controlled = false;
    var target, activeWedge, stage, layer, wheel,
            pointer, pointerTween, startRotation, startX, startY;


    function purifyColor(color) {
        var randIndex = Math.round(Math.random() * 3);
        color[randIndex] = 0;
        return color;
    }
    function getRandomColor() {
        var r = 100 + Math.round(Math.random() * 55);
        var g = 100 + Math.round(Math.random() * 55);
        var b = 100 + Math.round(Math.random() * 55);
        var color = [r, g, b];
        color = purifyColor(color);
        color = purifyColor(color);

        return color;
    }
    function bind() {
        wheel.on('mousedown', function(evt) {
            var mousePos = stage.getPointerPosition();
            angularVelocity = 0;
            controlled = true;
            target = evt.targetNode;
            startRotation = this.rotation();
            startX = mousePos.x;
            startY = mousePos.y;
        });
        // add listeners to lottery_container
        document.body.addEventListener('mouseup', function() {
            controlled = false;

            if (angularVelocity > MAX_ANGULAR_VELOCITY) {
                angularVelocity = MAX_ANGULAR_VELOCITY;
            }
            else if (angularVelocity < -1 * MAX_ANGULAR_VELOCITY) {
                angularVelocity = -1 * MAX_ANGULAR_VELOCITY;
            }

            angularVelocities = [];
        }, false);

        document.body.addEventListener('mousemove', function(evt) {
            var mousePos = stage.getPointerPosition();
            if (controlled && mousePos && target) {
                var x1 = mousePos.x - wheel.x();
                var y1 = mousePos.y - wheel.y();
                var x2 = startX - wheel.x();
                var y2 = startY - wheel.y();
                var angle1 = Math.atan(y1 / x1) * 180 / Math.PI;
                var angle2 = Math.atan(y2 / x2) * 180 / Math.PI;
                var angleDiff = angle2 - angle1;

                if ((x1 < 0 && x2 >= 0) || (x2 < 0 && x1 >= 0)) {
                    angleDiff += 180;
                }

                wheel.setRotation(startRotation - angleDiff);
            }
        }, false);
    }
    function getRandomReward() {
        var mainDigit = Math.round(Math.random() * 9);
        return mainDigit + '\n0\n0';
    }
    var rewardIndex = 0;
    var rewardArray = <?php echo json_encode($rewardArray)?>;
    var rewardRadius = <?php echo json_encode($rewardRadius)?>;
    function addWedge(n) {
        var s = getRandomColor();
        var reward = rewardArray[rewardIndex];
        rewardIndex++;
        var r = s[0];
        var g = s[1];
        var b = s[2];
        var angle = 360 / NUM_WEDGES;

        var endColor = 'rgb(' + r + ',' + g + ',' + b + ')';
        r += 100;
        g += 100;
        b += 100;

        var startColor = 'rgb(' + r + ',' + g + ',' + b + ')';

        var wedge = new Kinetic.Group({
            rotation: n * 360 / NUM_WEDGES
        });

        var wedgeBackground = new Kinetic.Wedge({
            radius: WHEEL_RADIUS,
            angle: angle,
            fillRadialGradientStartRadius: 0,
            fillRadialGradientEndRadius: WHEEL_RADIUS,
            fillRadialGradientColorStops: [0, startColor, 1, endColor],
            fill: '#64e9f8',
            fillPriority: 'radial-gradient',
            stroke: '#ccc',
            strokeWidth: 2,
            rotation: (90 + angle / 2) * -1
        });

        wedge.add(wedgeBackground);

        var text = new Kinetic.Text({
            text: reward,
            fontFamily: 'Calibri',
            fontSize: 30,
            fill: 'white',
            align: 'center',
            stroke: 'yellow',
            strokeWidth: 1,
            listening: false

        });

        text.offsetX(text.width() / 2);
        text.offsetY(WHEEL_RADIUS - 15);

        wedge.add(text);

        wheel.add(wedge);
    }

    var activeWedge;

    function animate(frame) {
        // wheel
        var angularVelocityChange = angularVelocity * frame.timeDiff * (1 - ANGULAR_FRICTION) / 1000;
        angularVelocity -= angularVelocityChange;

        if (controlled) {
            angularVelocity = ((wheel.getRotation() - lastRotation) * 1000 / frame.timeDiff);
        }
        else {
            wheel.rotate(frame.timeDiff * angularVelocity / 1000);
        }
        lastRotation = wheel.getRotation();

        // pointer
        var intersectedWedge = layer.getIntersection({x: stage.width() / 2, y: 50});

        if (intersectedWedge && (!activeWedge || activeWedge._id !== intersectedWedge._id)) {
            pointerTween.reset();
            pointerTween.play();
            activeWedge = intersectedWedge;
        }
        if(angularVelocity < 0.1) {
            anim.stop();
            $.colorbox({
                html: result_message,
                width : "90%",
                'onClosed' : function () {
                    blockUI("Đang chuyển trang, vui lòng chờ...");
                    location.reload();
                }
            });
        } 
        console.log(angularVelocity);

    }
    var layer;
    var anim;
    function init() {
        stage = new Kinetic.Stage({
            container: 'lottery_container',
            width: 400,
            height: 450
        });
        layer = new Kinetic.Layer();
        wheel = new Kinetic.Group({
            x: stage.getWidth() / 2,
            y: WHEEL_RADIUS + 20
        });

        for (var n = 0; n < NUM_WEDGES; n++) {
            addWedge(n);
        }
        pointer = new Kinetic.Wedge({
            fillRadialGradientStartPoint: 0,
            fillRadialGradientStartRadius: 0,
            fillRadialGradientEndPoint: 0,
            fillRadialGradientEndRadius: 30,
            fillRadialGradientColorStops: [0, 'white', 1, 'red'],
            stroke: 'white',
            strokeWidth: 2,
            lineJoin: 'round',
            angle: 30,
            radius: 30,
            x: stage.getWidth() / 2,
            y: 20,
            rotation: -105,
            shadowColor: 'black',
            shadowOffset: {x: 3, y: 3},
            shadowBlur: 2,
            shadowOpacity: 0.5
        });

        // add components to the stage
        layer.add(wheel);
        layer.add(pointer);
        stage.add(layer);

        pointerTween = new Kinetic.Tween({
            node: pointer,
            duration: 0.1,
            easing: Kinetic.Easings.EaseInOut,
            y: 30
        });

        pointerTween.finish();

        var radiusPlus2 = WHEEL_RADIUS + 2;

        wheel.cache({
            x: -1 * radiusPlus2,
            y: -1 * radiusPlus2,
            width: radiusPlus2 * 2,
            height: radiusPlus2 * 2
        }).offset({
            x: radiusPlus2,
            y: radiusPlus2
        });

        layer.draw();
        // bind events
        bind();
        anim = new Kinetic.Animation(animate, layer);
        $("#lottery_container").append('<div class="play_icon" onclick="play()"></div>');


    }
    function processRadius(coin) {
        
        //var arr = {1 : 0,10 : 30, 9 : 60, 8 : 90, 7 : 120, 6 : 150, 5 : 180, 4 : 210, 3 : 240 , 2 : 270, 1 : 300};
        return rewardRadius[coin];
    }
    var course_module = '<?php echo $course_module->id?>';
    var result_message = "";
    function play() {


        //document.getElementById('debug').appendChild(layer.hitCanvas._canvas);

        // wait one second and then spin the wheel
        //init();
        blockUI("Đang kiểm tra thông tin số lần quay của bạn...");
        $.ajax({
            type: 'GET',
            cache: false,
            url: "/mod/rtw/ajax.php?id="+course_module+"&c=lottery&a=result",
            success: function(response) {
                $.unblockUI();
                if(response.response_code == "1") {
                    angularVelocity = 595 + processRadius(response.response_data);
                    result_message = '<h2 style="color: green;text-align:center">'+response.response_message+'</h2>';
                    anim.start();
                } else {
                    result_message = '<h2 style="color: red;text-align:center">'+response.response_message+'</h2>';
                    $.colorbox({
                        html: result_message,
                        width : "90%",
                        'onClosed' : function () {
                            blockUI("Đang chuyển trang, vui lòng chờ...");
                            location.reload();
                        }
                    });
                }
            }
        });
        //angularVelocity = 600 + processRadius(8);
        
    }
    init();

</script>