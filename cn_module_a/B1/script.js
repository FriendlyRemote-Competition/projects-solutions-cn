const startColorsContainer = document.getElementById('startColors');
const startColor = document.getElementById('startColor');
const endColorsContainer = document.getElementById('endColors');

const endColor = document.getElementById('endColor');


const colors = "ABCDEF1234567890";
const gradientBox = document.getElementById('gradientBox');

function randomColor() {
    let str = '#';
    for (let i = 0; i < 6; i++) {
        console.log(Math.floor(Math.random() * colors.length))
        str += colors[Math.floor(Math.random() * colors.length)];
    }

    return str;
}


for (let i = 0; i < 8; i++) {
    const startInput = document.createElement('button');
    const endInput = document.createElement('button');
    let startColorValue = randomColor();
    let endColorValue = randomColor();

    startInput.classList.add('color-button');
    endInput.classList.add('color-button');

    startInput.style.background = startColorValue;
    endInput.style.background = endColorValue;

    startInput.addEventListener('click', function () {
        startColor.value = startColorValue;
        gradientBox.style.background = `linear-gradient(to right,${startColor.value},${endColor.value})`
    });

    endInput.addEventListener('click', function () {
        endColor.value = endColorValue;
        gradientBox.style.background = `linear-gradient(to right,${startColor.value},${endColor.value})`
    });

    startColorsContainer.appendChild(startInput);
    endColorsContainer.appendChild(endInput);
}

// add the listener

startColor.addEventListener('input', () => {
    gradientBox.style.background = `linear-gradient(to right,${startColor.value},${endColor.value})`

})
endColor.addEventListener('input', () => {
    gradientBox.style.background = `linear-gradient(to right,${startColor.value},${endColor.value})`

})


