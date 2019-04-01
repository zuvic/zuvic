$(document).ready(function() {
  var $nav = $('.nav');
  var $logo = $('.logo');
  var $logo_small = $('.logo_small');

  var currentSlide = 0;
  var quoteIndex = 0;
  var slideTitles = ['SITE/CIVIL', 'TRANSPORTATION', 'CONSTRUCTION', 'PERMITTING', 'SITE ASSESSMENTS', 'REMEDIATION', 'HAZARDOUS MATERIALS', 'ASTs/USTs'];

  var quoteAuthor = ['Goodwin College', 'CDECCA'  ];
  
  var quoteSubtitle = ['Mark Scheinberg | President', 'Mike Baier | Plant Manager'];

  var quoteText = ["Your extraordinary work has been crucial to the progress of Goodwin College’s riverfront campus project, and we are truly grateful ... Thank you for your hard work as a member of our River Campus construction team.", 
                    "Your firm’s impressive knowledge and experience ... was critical in presenting feasible options to overcome last minute obstacles and moving our [Hartford Cogeneration Plan Upgrade] project forward."];

  // Initial pageload things
  window.scrollTo(0, 0);

  // Navbar load
  $nav.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(e) {
      if (e['originalEvent']['propertyName'] == "width") {
          var scrollTop = $(window).scrollTop();

          if (scrollTop <= 30) {
                $nav.removeClass('collapsed');
                $logo.removeClass('collapsed');
                $logo_small.removeClass('collapsed');
                $nav.addClass('open');
                $logo.addClass('open');
                $logo_small.addClass('open');
          } else {
                $nav.addClass('collapsed');
                $logo.addClass('collapsed');
                $logo_small.addClass('collapsed');
                $nav.addClass('open');
                $logo.addClass('open');
                $logo_small.addClass('open');
          }
      }
      if (e['originalEvent']['propertyName'] == "height") {
          $nav.unbind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd");
      }
  });

  // Navbar link effects
  $('.nav .links').hover(function(e) {
      $(this).css('color', 'var(--red)');
      $(this).find('.underline').css('width', '100%');
      $(this).find('.underline').css('margin-left', '0%');
  }, function() {
      $(this).css('color', 'var(--blue)');
      $(this).find('.underline').css('width', '0%');
      $(this).find('.underline').css('margin-left', '50%');
      $(this).find('.underline').css('left', 'calc(50vw - 115px)');
  });

  // Setup fixed navbar handler
  $nav.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(e) {
      if (e['originalEvent']['propertyName'] == "height") {
          $(window).scroll(function() {
              var scrollTop = $(this).scrollTop();

              if (scrollTop <= 30) {
                  $nav.removeClass('collapsed');
                  $logo.removeClass('collapsed');
                  $logo_small.removeClass('collapsed');
                  $('#contact-button').removeClass('hover');
              } else {
                  $nav.addClass('collapsed');
                  $logo.addClass('collapsed');
                  $logo_small.addClass('collapsed');
                  $('#contact-button').addClass('hover');
              }
          });
      }
  });

  // Hamburger Handler
  $('.navTrigger').click(function () {
    $(this).toggleClass('active');
    $('#nav-mobile').toggleClass('active');
  });

  // Careers Drawer
  $('.drawer-button').click(function() {
    $(this).next('.drawer').toggleClass('open');
  });

  // Home Page services slider
  $('.row.slider .slide').on('mouseover', function(e) {
      if(isMobileDevice.any()) return;
      var $this = $(this);
      var thisIndex = $this.index();
      var $caption = $('.row.slider .sub-caption .text');

      if(thisIndex != currentSlide) {
          $caption.css('margin-top', '-90px');
              $caption.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(e) {
                  if (e['originalEvent']['propertyName'] == "margin-top") {
                      $caption.text(slideTitles[thisIndex]);
                      $caption.css('margin-top', '0px');
                      currentSlide = thisIndex;
                  }
              });
      }

      $('.row.slider .slide').css('width', '80px');
      $this.css('width', '100%');
  });

  // Clients Slider
  setInterval(function() {
      var $client = $('.row.clients .client');
      var $clientTitle = $('.row.clients .client .title');
      var $clientSubtitle = $('.row.clients .client .subtitle');
      var $clientText = $('.row.clients .testimonials .text');

      $client.css('margin-top', '-80px');
      $client.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(e) {
          if (e['originalEvent']['propertyName'] == "margin-top") {
            $clientTitle.text(quoteAuthor[quoteIndex]);
            $clientSubtitle.text(quoteSubtitle[quoteIndex]);

            $client.css('margin-top', '0px');
          }
      });

      $clientText.css('top', '150%');
      $clientText.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(e) {
          if (e['originalEvent']['propertyName'] == "top") {
              $clientText.text('"' + quoteText[quoteIndex] + '"');

              $clientText.css('top', '50%');
          }
      });

      quoteIndex++;
      if(quoteIndex == quoteAuthor.length) quoteIndex = 0;
  }, 6500);

  $('.row.related .project').hover(function () {
    $(this).toggleClass('hover');
  }, function () {
    $(this).toggleClass('hover');
  });

  $nav.css('width', '100%');
});

function generateProjImgs(projNum, imgNum) {
    var allImages = "";
    var elem = document.querySelector('.m-p-g');
  
    //id="img_' + projNum + '_' + i + '"
    for (var i = 1; i <= imgNum; i++) {
      allImages += '<img src="/images/' + projNum + '/' + i + '.jpg" data-full="/images/' + projNum + '/' + i + '.jpg" class="m-p-g__thumbs-img">';
    }
    
    $('.m-p-g__thumbs').append(allImages);

    // for (var i = 1; i <= imgNum; i++) {
    //     var width = getRandomSize(400, 600);
    //     var height =  getRandomSize(400, 600);
    //     $('#img_' + projNum + '_' + i).css('width', width + 'px');
    //     $('#img_' + projNum + '_' + i).css('height', height + 'px');
    // }

    return new MaterialPhotoGallery(elem);
}

function generateServImgs(servProjects) {
    var allImages = "";
    var elem = document.querySelector('.m-p-g');
  
    //id="img_' + projNum + '_' + i + '"
    for (i = 0; i < servProjects.length; i++) {
        $.ajax  ({
            url: '/images/' + servProjects[i]['id'] + '/1.jpg',
            async: false 
        })
        .done(function() { 
            allImages += '<img src="/images/' + servProjects[i]['id'] + '/1.jpg" data-full="/images/' + servProjects[i]['id']  + '/1.jpg" class="m-p-g__thumbs-img">';
        }).fail(function() { 
            allImages += '<img src="/images/blank.jpg" data-full="/images/blank.jpg" class="m-p-g__thumbs-img">';
        });
    }
    
    $('.m-p-g__thumbs').append(allImages);

    return new MaterialPhotoGallery(elem);
}

function getRandomSize(min, max) {
    return Math.round(Math.random() * (max - min) + min);
}

function isScrolledIntoView(elem) {
  var docViewTop = $(window).scrollTop();
  var docViewBottom = docViewTop + $(window).height();

  var elemTop = $(elem).offset().top;
  var elemBottom = elemTop + $(elem).height();

  return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop) || (elemTop <= docViewTop && elemBottom >= docViewTop) || (elemTop <= docViewBottom && elemBottom >= docViewBottom));
}

function getSliderImgPath($this) {
  var cSrc = $this.src.split(/.jpg$/);

  return isMobileDevice.any() ? cSrc[0] + '_mobile.jpg' : cSrc[0] + '.jpg';
}

// Credit TwoFuckingDevelopers: https://coderwall.com/p/i817wa/one-line-function-to-detect-mobile-devices-with-javascript
var isMobileDevice = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobileDevice.Android() || isMobileDevice.BlackBerry() || isMobileDevice.iOS() || isMobileDevice.Opera() || isMobileDevice.Windows());
    },
    none: function() {
        return (!isMobileDevice.any());
    }
};