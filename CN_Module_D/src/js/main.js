/**
 *  hotel story animation and fetch data
 */
function hotelStory() {
    function animateNumber($el, target, duration = 1500) {
        const startTime = performance.now();

        function update(currentTime) {
            const progress = Math.min((currentTime - startTime) / duration, 1);

            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(target * eased);
            $el.text(current);
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                $el.text(target);
            }
        }

        requestAnimationFrame(update);
    }

    fetch('public/media_files/content/hotel-copy.json').then(res => {
        if (res.ok) {
            return res.json();
        }
    }).then(data => {
        $(".hotel-secondary-title").html(data.eyebrow);
        $(".hotel-title").html(data.heading);
        $(".hotel-content").html(data.body);

        $('.rooms').html(data.stats[0].value);
        $('.rooms + .label').html(data.stats[0].label);

        $('.restaurants').html(data.stats[1].value);
        $('.restaurants ~ .label').html(data.stats[1].label);

        $('.hosts').html(data.stats[2].value);
        $('.hosts + .label').html(data.stats[2].label);
        $('.hosts + .suffix').html(data.stats[2].suffix);


        /**
         *
         * @type {IntersectionObserver}
         */
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const $el = $(entry.target);
                const target = Number($el.data('target'));
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    $el.text(target);
                } else {
                    animateNumber($el, target, 1500);
                }
                observer.unobserve(entry.target);
            });
        });


        $('.rooms').data('target', data.stats[0].value).text(0).each((_, el) => observer.observe(el));

        $('.restaurants').data('target', data.stats[1].value).text(0).each((_, el) => observer.observe(el));

        $('.hosts').data('target', data.stats[2].value).text(0).each((_, el) => observer.observe(el));
    })
}


function initRoomsPagination() {
    let currentIndex = 0;
    let changing = false;

    const $rooms = $(".rooms-item");
    const total = $rooms.length;

    function changeRoom(nextIndex) {
        if (
            nextIndex < 0 ||
            nextIndex >= total ||
            nextIndex === currentIndex ||
            changing
        ) {
            return;
        }

        changing = true;

        const $currentRoom = $(".rooms-item.active");
        const $nextRoom = $(`.rooms-item[data-index="${nextIndex}"]`);

        const direction = nextIndex > currentIndex ? "next" : "prev";

        $(".paginate-item").removeClass("active");
        $(`.paginate-item[data-index="${nextIndex}"]`).addClass("active");

        if (direction === "next") {
            $currentRoom.addClass("leave-left");
            $nextRoom.addClass("prepare-right");
        } else {
            $currentRoom.addClass("leave-right");
            $nextRoom.addClass("prepare-left");
        }

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                $nextRoom.addClass("active");

                $nextRoom.removeClass(
                    "prepare-right prepare-left"
                );
            });
        });

        setTimeout(() => {
            $currentRoom.removeClass(
                "active leave-left leave-right"
            );

            currentIndex = nextIndex;

            updateButtons();

            changing = false;
        }, 400);
    }

    function updateButtons() {
        $(".prev").prop(
            "disabled",
            currentIndex === 0
        );

        $(".next").prop(
            "disabled",
            currentIndex === total - 1
        );
    }

    // 点击分页
    $(".paginate-item").on("click", function () {
        const index = Number($(this).data("index"));

        changeRoom(index);
    });

    $(".prev").on("click", function () {
        changeRoom(currentIndex - 1);
    });

    $(".next").on("click", function () {
        changeRoom(currentIndex + 1);
    });

    $(document).on("keydown", function (e) {
        if (e.key === "ArrowLeft") {
            $(`.paginate-item[data-index=${currentIndex - 1}]`).trigger("focus");
            changeRoom(currentIndex - 1);
        }

        if (e.key === "ArrowRight") {
            $(`.paginate-item[data-index=${currentIndex + 1}]`).trigger("focus");
            changeRoom(currentIndex + 1);
        }
    });

    updateButtons();
}

/**
 *  room data
 */
function roomsData() {
    fetch('public/media_files/data/rooms.json').then(res => {
        if (res.ok) {
            return res.json();
        }
    }).then(data => {
        const roomsContainer = $(".rooms-container");
        roomsContainer.html('');
        $(".paginate-list").html('');
        data.forEach((item, index) => {
            roomsContainer.append(`
                <div 
                data-index="${index}"
                id="${item.id}" class="rooms-item  ${index === 0 ? 'active' : ''}">
                                  <img src="public/media_files/${item.image}" alt="${item.name}">
                                  <div class="rooms-right">
                                    <h3 class="room-name">${item.name}</h3>
                                    <p>${item.description}</p>
                                    <hr>
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col">
                                        <span>SPACE</span>
                                        <span>${item.size}</span>
</div>
  <div class="flex flex-col">
                                        <span>GUESTS</span>
                                        <span>${item.guests}</span>
</div>
  <div class="flex flex-col">
                                        <span>BED</span>
                                        <span>${item.bed}</span>
</div>
</div>
                                    <hr>
                                    
                                    <h4>From CNY${new Intl.NumberFormat('en-US').format(item.price)}/night</h4>
                                    <div class='flex gap-1 items-center '>
                                    <button    data-id="${item.id}" aria-controls="amenities-${item.id}" aria-expanded="false" data-index="${index}" class="room-item-secondary">VIEW ROOM DETAILS</button>
                                    <button class="room-item-btn">BOOK THIS ROOM</button>
</div>   
<div class="amenities-list" id="amenities-${item.id}"   hidden></div>
</div>
                    </div> 
            `);
            $(".paginate-list").append(`
                    <button data-id="${item.id}" data-index="${index}"  class="paginate-item ${index === 0 ? 'active' : ''}">
                               ${item.name}
</button> 
            `);

        });

        roomsContainer.on("click", ".room-item-secondary", function () {
            const $button = $(this);
            const id = $button.data("id");
            const room = data.find(item => item.id === id);
            if (!room) return;
            const controlsId = $button.attr("aria-controls");

            const $details = $("#" + controlsId);

            const expanded = $button.attr("aria-expanded") === "true";


            if (expanded) {
                $button.attr("aria-expanded", "false");

                $details.prop("hidden", true);

                return;
            }


            $button.attr("aria-expanded", "true");

            const amenitiesHtml = room.amenities
                .map(amenity => `
            <li>${amenity}</li>
        `)
                .join("");

            $details.html(`
        <h4>AMENITIES</h4>

        <ul>
            ${amenitiesHtml}
        </ul>
    `);

            $details.prop("hidden", false);
        });
        initRoomsPagination();
    });
}

roomsData();


/**
 * header scroll
 */
function headerScroll() {
    window.addEventListener('scroll', function (e) {
        const hero = $("#hero");
        const triggerPoint = hero.offset().top + hero.outerHeight() * 0.2;
        if (window.scrollY >= triggerPoint) {
            $('header').addClass("active");
        } else {
            $('header').removeClass("active");
        }
    });
}

headerScroll();

hotelStory();


/**
 * dining data load
 */
function diningData() {
    fetch('public/media_files/content/dining-copy.json').then(res => {
        if (res.ok) {
            return res.json();
        }
    }).then(data => {
        const diningList = $('.dining-list');
        diningList.html('');

        diningList.append(`
               <article class="dining-item" tabindex="0">
                <img src="public/media_files/images/dining-su-kitchen.webp" alt="dining-su-kitchen">
                <div class="dining-content">
                <h4 class="secondary-title">${data['su-kitchen'].eyebrow}</h4>
                <h3>${data['su-kitchen'].name}</h3>
                <div class="detail">
                <p>${data['su-kitchen'].description}</p>
                <p>${data['su-kitchen'].hours}</p>
</div>
                </div>
</article>  
        `);

        diningList.append(`
               <article  class="dining-item"  tabindex="0">
                <img src="public/media_files/images/dining-river-bar.webp" alt="dining-river-bar">
                <div  class="dining-content">
                <h4 class="secondary-title">${data['river-bar'].eyebrow}</h4>
                <h3>${data['river-bar'].name}</h3>
                <div class="detail">
                <p>${data['river-bar'].description}</p>
                <p>${data['river-bar'].hours}</p>
</div>
                </div>
</article>  
        `);
    });
}

diningData();


//shanghai


function shanghaiData() {
    fetch('public/media_files/data/nearby.json').then(res => {
        if (res.ok) {
            return res.json();
        }
    }).then(data => {
        console.log(data);
        $("#shanghai .right").html('');
        data.forEach(item => {
            $("#shanghai .right").append(`
                    <div data-id="${item.markerId}" tabindex="0" class="shanghai-item">
                            <img  width="100" src="public/media_files/${item.image}" alt="${item.name}">
                            <div class="flex flex-col">
                              <h3>${item.name}</h3>  
                           <p>${item.description}</p>
</div>
                          
                            
</div>
            `);
        })
    })
}

shanghaiData();


//footer

function footerData() {
    fetch('public/media_files/content/footer-copy.json').then(res => {
        if (res.ok) {
            return res.json();
        }
    }).then(data => {
        data.social.forEach(social => {
            $(".social").append(`
                <a href="#">${social}</a>
            `);
        });
        $('.address').html(data.address);
        $('.phone').html(data.phone);
        $('.email').html(data.email);

        data.navigation.forEach(link => {
            $("footer .navigation").append(`
                <a href="#">${link}</a>
            `);
        })
        $('.copyright').html(`&copy; ${new Date().getFullYear()} The Sù Hotel Shanghai`);
    });
}


footerData();


function sidebar() {
    $('.menu-btn').on("click", function () {
        if ($("#sidebar").hasClass("show")) {
            $('#sidebar').removeClass('show');
            $(this).attr("aria-expanded", "false");
        } else {
            $('#sidebar').addClass('show');
            $(this).attr("aria-expanded", "true");
        }
    });
    $(".close").on("click", function () {
        $('#sidebar').removeClass('show');
        $('.menu-btn').attr("aria-expanded", "false");
    });

    $(document).on("keydown", function (e) {
        if (e.code === "Escape") {
            $('#sidebar').removeClass('show');
            $('.menu-btn').attr("aria-expanded", "false");
        }
    })
}

sidebar();
