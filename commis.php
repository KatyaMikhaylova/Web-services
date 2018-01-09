<?php
function viewInit($file, $arr = [])
{
$file = $file . '.html';
include "view/$file";
}
viewInit('commis');

        if (isset($_GET['data']) && isset($_GET['num'])&& isset($_GET['addr'])) {
            $data = $_GET['data'];
            $num = $_GET['num'];
            $addr=$_GET['addr'];
        }
        ?>

<?php

if (isset($_POST) && (!empty($_POST))) {

    $num = pow(sizeof($_POST), 1 / 2);
    function getData($n)
    {

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if (isset($_POST["{$i}{$j}"]) && $_POST["{$i}{$j}"] != 0 && $_POST["{$i}{$j}"] != "M") {
                    $arr[$i][$j] = (int)($_POST["{$i}{$j}"]);
                } elseif ($_POST["{$i}{$j}"] == "M") {
                    $arr[$i][$j] = INF;
                } else {
                    print "Заполните все поля таблицы";
                    break 2;
                }

            }

        }
        return $arr;
    }

    $start = $arr = getData($num);//сохраняем изначальный массив, чтобы посчитать в конце вес оптимального пути

    do {

        if (!function_exists('Reduce')) {
            function Reduce($array) // находим минимальное значение в каждой сторке и вычитаем

                // из каждого элемента строки найденное минимальное знач-е
            {
                $x = 0;
                foreach ($array as $val) {

                    $minrow[$x] = min($val);
                    $x++;

                }
                for ($j = 0; $j < sizeof($array); $j++) {
                    for ($i = 0; $i < sizeof($array[$j]); $i++) {
                        if ($array[$j][$i] != INF) {
                            $array[$j][$i] -= $minrow[$j];
                        } else {
                            $array[$j][$i] = INF;
                        }
                    }

                }
                return $array;
            }
        }

        $arr = Reduce($arr);

        if (!function_exists('Reverse')) {
            function Reverse($array) //меняем местами строки и столбцы
            {
                for ($i = 0; $i < sizeof($array); $i++) {
                    for ($j = 0; $j < sizeof($array[$i]); $j++) {

                        $arrnew[$j][$i] = $array[$i][$j];

                    }
                }
                return $arrnew;
            }
        }
        $arrnew = Reverse($arr);
//находим минимальное значение в каждой строке (по смыслу-в столбце)бвычитаем из каждого элемента строки (столбца)
// найденное минимальное знач-е
        $arrnew = Reduce($arrnew);

        //возвращаем массив в исходное состояние, где строки-строки, а столбцы-столбцы
        $arr = Reverse($arrnew);

//составляем массив оценок нулевых клеток ($eval)
        for ($i = 0; $i < sizeof($arr); $i++) {
            for ($j = 0; $j < sizeof($arr[$i]); $j++) {
                if ($arr[$i][$j] == 0) { //находим нулевую клетку
                    $arr[$i][$j] = INF;//так как она сама не должна учитываться, присваиваем ей INF
                    $d1 = min($arr[$i]);//ищем минимальное значение в этой строке

                    $arr[$i][$j] = 0;//возвращаем клетке исходное нулевое значение
                    $arrnew[$j][$i] = INF;//то же делаем по столбцу (используя "столбцовый массив")

                    $d2 = min($arrnew[$j]);

                    $arrnew[$j][$i] = 0;
                    $eval[$i][$j] = ($d1 + $d2);//оценкой нулевой клетки будет сумма минумума по строке и столбцу

                } else {
                    $eval[$i][$j] = -1;//если клетка не нулевая, присваимваем соотв. элементу в массиве оценок -1
                    //т.к. там мы будем искать максимум, и это позволит нам не учитывать ненулевые клеткм
                }
            }
        }

//находим максимальную оценку (максиальный элемент $eval)
        $max = $eval[0][0];
        $i = 0;
        foreach ($eval as $arr1) {
            foreach ($arr1 as $v) {
                if ($max < $v) $max = $v;
                ++$i;
            }
        }

        //находим индекс этого элемента и записываем две цифры в массив "города", тк индексы - это номера городов
        //посещаемых коммивояжером
        for ($i = 0; $i < sizeof($eval); $i++) {
            for ($j = 0; $j < sizeof($eval[$i]); $j++)
                if ($eval[$i][$j] === $max) {
                    $m = $i;
                    $n = $j;

                    $cities[] = $m + 1;
                    $cities[] = $n + 1;

                    break 2;//выходим из цикла при нахождении первого элемента, соответствующего требованиям, чтобы
                    //лишние индексы не попали в массив города
                }
        }
        $arr[$n][$m] = INF;//Присваиваем "зеркальному" элементу значение "бесконечность"
//присваиваем всем элементам строки и столбца, где был элемент с макс. оценкой значение "бесконечность",
        //чтобы в дальнейшем их не рассматривать
        for ($i = 0; $i < sizeof($arr); $i++) {
            for ($j = 0; $j < sizeof($arr[$i]); $j++) {
                if ($i == $m) {
                    $arr[$i][$j] = INF;
                }
                if ($j == $n) {
                    $arr[$i][$j] = INF;
                }
            }
        }
    } while (sizeof($cities) <= $num * 2 - 2);


    $cities = array_chunk($cities, 2);
    $route = [];
    $first = $cities[0];

    $route[] = $first[0];
    $route[] = $first[1];
    $nextcity = $first[1];
    function getCity($city, $array)
    {
        foreach ($array as $c) {
            if ($c[0] == $city) {
                return $c;
            }
        }
    }


    do {
        $next = getCity($nextcity, $cities);
        $route[] = $next[0];
        $route[] = $next[1];
        $nextcity = $next[1];
    } while ($next[0] != $first[0]);
    $route2 = array_chunk($route, 2);
    $weight = 0;
    for ($i = 0; $i < count($route2) - 1; $i++) {
        $m = $route2[$i][0] - 1;
        $n = $route2[$i][1] - 1;
        $weight += $start[$m][$n];
    }
    $route = array_unique($route);
    ?>


    <h4>Оптимальный маршрут: </h4>
    <?php
    $address=json_decode($addr);
    foreach ($route as $val) {
        print $address[$val-1] . " (Пункт " .  $val . ') -> ';?><br>
        <?php
    }

    print $address[0] . "(Пункт " .  $route[0] . ")";
    ?>
    <h4>Длина оптиимального пути: </h4>
    <?php
    print $weight;
    print  " км";

}
?>
</div>
</div>
<div class="container container_footer col-sm-12">
    <div class="row">
        <div class="footer col-sm-12">

        </div>
    </div>
    </div>
  <script>
      var data = '<?php print $data;?>';
      var finalData = JSON.parse(data);
      var num= '<?php print $num;?>';




      DrawTable();


      function DrawTable() {
          var str = "";
          str += '<tr>';
          for (var i = 0; i <= num; i++) {
              if (i == 0) {
                  str += '<td></td>'
              } else {
                  str += '<td> Пункт ' + i + '</td>'
              }

          }
          str += '</tr>';

          str += '<tr>';
          for (i = 0; i <= num - 1; i++) {
              str += '<td> Пункт ' + (i + 1) +'  ' +'</td>';
              for (j = 0; j <= num - 1; j++) {
                  if (i == j) {

                      str += '<td><input style="background-color: #aaaaaa" name="' + i + j + '" value="M"></td>'
                  } else {
                      if (i < j) {
                          var m=i.toString()+j.toString();
                          str += '<td><input   type="number" name="' + i + j + '"  value="' + finalData[m] + '"></td>';
                      } else {
                          var m=j.toString()+i.toString();
                          str += '<td><input   type="number" name="' + i + j + '"  value="' + finalData[m] + '"></td>';
                      }
                  }


              }
              str += '</tr>';
          }
          document.querySelector("#table").innerHTML += str;
          document.querySelector(".hidden").classList.remove("hidden");
      }
  </script>
    </body>
    </html>

