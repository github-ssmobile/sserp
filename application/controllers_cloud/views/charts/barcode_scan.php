<style>
        .preloader-scan {
  position: fixed;
  left: 0;
  right: 0;
  max-width: 200px;
  display: table;
  margin: 0 auto;
  height: 100%;
  text-align: center;
}
.preloader-scan li:nth-child(1) {
  width: 2px;
}
.preloader-scan li:nth-child(2) {
  width: 2px;
}
.preloader-scan li:nth-child(3) {
  width: 1px;
}
.preloader-scan li:nth-child(4) {
  width: 2px;
}
.preloader-scan li:nth-child(5) {
  width: 5px;
}
.preloader-scan li:nth-child(6) {
  width: 4px;
}
.preloader-scan li:nth-child(7) {
  width: 5px;
}
.preloader-scan li:nth-child(8) {
  width: 1px;
}
.preloader-scan li:nth-child(9) {
  width: 1px;
}
.preloader-scan li:nth-child(10) {
  width: 4px;
}
.preloader-scan li:nth-child(11) {
  width: 5px;
}
.preloader-scan li:nth-child(12) {
  width: 5px;
}
.preloader-scan li:nth-child(13) {
  width: 5px;
}
.preloader-scan li:nth-child(14) {
  width: 1px;
}
.preloader-scan li:nth-child(15) {
  width: 1px;
}
.preloader-scan li:nth-child(16) {
  width: 4px;
}
.preloader-scan li:nth-child(17) {
  width: 4px;
}
.preloader-scan li:nth-child(18) {
  width: 1px;
}
.preloader-scan li:nth-child(19) {
  width: 4px;
}
.preloader-scan li:nth-child(20) {
  width: 5px;
}
.preloader-scan li:nth-child(21) {
  width: 1px;
}
.preloader-scan li:nth-child(22) {
  width: 5px;
}
.preloader-scan li:nth-child(23) {
  width: 2px;
}
.preloader-scan li:nth-child(24) {
  width: 1px;
}
.preloader-scan ul {
  height: 100%;
  display: table-cell;
  vertical-align: middle;
  list-style-type: none;
  text-align: center;
}
.preloader-scan li {
  display: inline-block;
  width: 2px;
  height: 50px;
  background-color: #444;
}
.preloader-scan .laser {
  width: 150%;
  margin-left: -25%;
  background-color: tomato;
  height: 1px;
  position: absolute;
  top: 40%;
  z-index: 2;
  box-shadow: 0 0 4px red;
  -webkit-animation: scanning 2s infinite;
          animation: scanning 2s infinite;
}
.preloader-scan .diode {
  -webkit-animation: beam .01s infinite;
          animation: beam .01s infinite;
}

body {
  height: 100%;
}

@-webkit-keyframes beam {
  50% {
    opacity: 0;
  }
}

@keyframes beam {
  50% {
    opacity: 0;
  }
}
@-webkit-keyframes scanning {
  50% {
    -webkit-transform: translateY(75px);
            transform: translateY(75px);
  }
}
@keyframes scanning {
  50% {
    -webkit-transform: translateY(75px);
            transform: translateY(75px);
  }
}

    </style>
    <div class="preloader-scan">
  <ul>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    
    <div class="diode">
      <div class="laser"></div>
    </div>
  </ul>
  
</div>