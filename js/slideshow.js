/**
 * Génère un slideshow à partir d'une liste de cartes
 */
// Tableau des intervales des différents slideshows
var slideShowIntervals = {};

// Démarre le passage automatique au slide suivant
function startSlideShow(slideShowId) {
    if (slideShowIntervals.hasOwnProperty(slideShowId)) {
        clearInterval(slideShowIntervals[slideShowId]);
    }
    slideShowIntervals[slideShowId] = setInterval(function() {
        document.querySelector('[data-id="' + slideShowId + '"] .right').click();
    }, 5000);
}

// Modifie le slide courant
function setCurrentSlide(slideShow, oldIndex, newIndex) {
    slideShow.setAttribute('data-index', newIndex);
    slideShow.getElementsByClassName('card')[oldIndex].classList.remove('show');
    slideShow.getElementsByClassName('card')[newIndex].classList.add('show');
    slideShow.getElementsByClassName('indicator')[oldIndex].classList.remove('show');
    slideShow.getElementsByClassName('indicator')[newIndex].classList.add('show');
    var slideShowId = slideShow.getAttribute('data-id');
    startSlideShow(slideShowId);
}

document.addEventListener('DOMContentLoaded', () => {
    Array.from(document.getElementsByClassName('slideshow')).forEach(function (slideShow) {
        // Configure le slideshow courant
        var id = (new Date().getUTCMilliseconds() + Math.random()).toString(36);
        var slides = Array.from(slideShow.getElementsByClassName('card'));
        slideShow.setAttribute('data-id', id);
        slideShow.setAttribute('data-index', '0');
        slideShow.setAttribute('data-slides', slides.length.toString());
        // Ajoute les boutons de controle
        var leftButton = document.createElement('a');
        leftButton.classList.add('control', 'left');
        leftButton.innerHTML = '&#10094;';
        leftButton.addEventListener('click', function() {
            var index = parseInt(this.parentNode.getAttribute('data-index'));
            var slidesCount = parseInt(this.parentNode.getAttribute('data-slides'));
            var newIndex = index - 1
            if (newIndex < 0) {
                newIndex = slidesCount - 1;
            }
            setCurrentSlide(this.parentNode, index, newIndex);
        });
        slideShow.appendChild(leftButton);
        var rightButton = document.createElement('a');
        rightButton.classList.add('control', 'right');
        rightButton.innerHTML = '&#10095;';
        rightButton.addEventListener('click', function() {
            var index = parseInt(this.parentNode.getAttribute('data-index'));
            var slidesCount = parseInt(this.parentNode.getAttribute('data-slides'));
            var newIndex = index + 1
            if (newIndex === slidesCount) {
                newIndex = 0;
            }
            setCurrentSlide(this.parentNode, index, newIndex);
        });
        slideShow.appendChild(rightButton);
        // Premier slide affiché par défaut
        if (slides.length > 0) {
            slides[0].classList.add('show');
        }
        // Ajoute les indicateurs
        var indicatorContainer = document.createElement('div');
        indicatorContainer.classList.add('indicator-container');
        for (var indicatorIndex = 0; indicatorIndex < slides.length; ++indicatorIndex) {
            var indicator = document.createElement('div');
            if (indicatorIndex === 0) {
                indicator.classList.add('show');
            }
            indicator.classList.add('indicator')
            indicator.setAttribute('data-index', indicatorIndex.toString());
            indicator.addEventListener('click', function() {
                var index = parseInt(this.parentNode.parentNode.getAttribute('data-index'));
                var newIndex = parseInt(this.getAttribute('data-index'));
                setCurrentSlide(this.parentNode.parentNode, index, newIndex);
            });
            indicatorContainer.appendChild(indicator);
        }
        slideShow.appendChild(indicatorContainer);
        startSlideShow(id);
    });
});