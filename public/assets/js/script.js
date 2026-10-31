

// >>-- 01 Horizontal Nav Js --<<
// `var` (not `let`/`const`) because Turbo Drive re-runs this whole file on every
// in-app navigation (it has to, to rebind to the fresh nav/sidebar DOM each time) —
// `let`/`const` at top level would throw "already declared" on the second run.
var navBar = $(".main-nav");
var size = "150px";
var leftsideLimit = -100;
var navbarSize;
var containerWidth;
var maxNavbarLimit;

// Clear delegated handlers this file registered on a previous Turbo visit before
// re-registering them below, so they don't stack up (document itself is never
// replaced by Turbo, unlike the rest of the page).
$(document).off(".appJs");

function setUpHorizontalHeader() {
  navbarSize = navBar.width();
  containerWidth = ($(".simplebar-content").width())
  maxNavbarLimit = -(navbarSize - containerWidth);
  if ($("nav").hasClass("horizontal-sidebar")) {
    $(".menu-next").removeClass("d-none");
    $(".menu-previous").removeClass("d-none");
  } else {
    navBar.css("marginLeft",0)
    $(".menu-next").addClass("d-none");
    $(".menu-previous").addClass("d-none");
  }
  $(".horizontal-sidebar .show").removeClass("show");
}

$(document).on('click.appJs', '.menu-previous', function (e) {
  let layoutOption = getLocalStorageItem("layout-option","ltr");
  let attribute = layoutOption == 'ltr' ? 'marginLeft' : 'marginRight';
  let currentPosition = parseInt(navBar.css(attribute));
  if (currentPosition < 0) {
    navBar.css(`${attribute}`, "+=" + size)
    $(".menu-next").removeClass("d-none");
    $(".menu-previous").removeClass("d-none");
    if (currentPosition >= leftsideLimit) {
      $(this).addClass("d-none");
    }
  }
})

$(document).on('click.appJs', '.menu-next', function (e) {
  let layoutOption = getLocalStorageItem("layout-option","ltr");
  let attribute = layoutOption == 'ltr' ? 'marginLeft' : 'marginRight';
  let currentPosition = parseInt(navBar.css(attribute));
  if (currentPosition >= maxNavbarLimit) {
    $(".menu-next").removeClass("d-none");
    $(".menu-previous").removeClass("d-none");
    navBar.css(`${attribute}`, "-=" + size)
    if (currentPosition - parseInt(size) <= maxNavbarLimit) {
      $(this).addClass("d-none");
    }
  }
})

$(function () {
  setUpHorizontalHeader();
  let themeMode = getLocalStorageItem('theme-mode', 'light')
  setTimeout(() => {
    $('body').addClass(`${themeMode}`)
  }, 1000);
});


// >>-- 02 Flag  Icon Js --<<
$(function () {
  var text = $(".selected i").attr('class')
  $(".flag i").prop('class', text);
  $(document).on('click.appJs', '.lang', function () {
    $(".lang").removeClass("selected");
    $(this).addClass("selected");
    text = $(".selected i").attr('class')
    $(".flag i").prop('class', text);
  });
})



// >>-- 03 Copy js --<<
function copyvalue() {
  var temp = document.createElement('input');
  var texttoCopy = document.getElementById('copyText2').innerHTML;
  temp.type = 'input';
  temp.setAttribute('value', texttoCopy);
  document.body.appendChild(temp);
  temp.select();
  document.execCommand("copy");
  temp.remove();
  console.timeEnd('time2');
}



// >>-- 04 Sidebar toggle js --<<
// Persisted so the mini/expanded choice survives a reload instead of resetting
// to expanded every time (default is minimized for first-time visitors).
$(document).on('click.appJs', '.header-toggle', function () {
  var isMini = $("nav").toggleClass("semi-nav").hasClass("semi-nav");
  setLocalStorageItem('sidebar-mini', isMini ? '1' : '0');
});
$(".toggle-semi-nav").on("click", function () {
  $("nav").removeClass("semi-nav");
  setLocalStorageItem('sidebar-mini', '0');
});


// >>-- 05 List page js --<<
$(".contact-listbox").on("click", function () {
  $(this).toggleClass("stared");
});

function resize() {
  var $window = $(window),
    $nav = $('nav');

  if ($window.width() < 768) {
    $nav.removeClass('semi-nav');
  } else if ($window.width() < 1199) {
    $nav.addClass('semi-nav');
  } else {
    // Desktop width: restore whatever mini/expanded state was last saved,
    // defaulting to minimized rather than always resetting to expanded.
    var isMini = getLocalStorageItem('sidebar-mini', '1') === '1';
    $nav.toggleClass('semi-nav', isMini);
  }
}

$(function () {
  resize();
});
window.addEventListener("resize", () => {
  resize();
});


// >>-- 06 Sidebar scroll js --<<
// Guarded: pages on layout/fullscreen (no navbar) don't have #app-simple-bar
// at all, and SimpleBar's constructor throws on a null element.
var myElement = document.getElementById('app-simple-bar');
if (myElement) {
  new SimpleBar(myElement, { autoHide: true });
}

// Sidebar active class js
$(function () {
  let current = location.pathname;
  current = current.substring((current.lastIndexOf('/')) + 1);
  $('.main-nav li a').each(function () {
    var $this = $(this);
    if (current === $this.attr("href").split('/').pop()) {
      if ($this.parent().parent().parent().hasClass("another-level")) {
        $this.parent().parent().parent().parent().closest('li').children().addClass('show').attr("aria-expanded", "true");
      }
      $this.parent().parent().parent().children().addClass('show');
      $this.parent().parent().parent().children().attr("aria-expanded", "true");
      $this.parent('li').addClass('active');
    }
  })
})

// >>-- 07 Loader JS --<<
// Only animate the boot splash on the very first real page load. On later Turbo
// visits a fresh .loader-wrapper is part of every page's body (it's in main.blade.php),
// so without this guard it would fade in/out on every single in-app navigation.
if (!window.__appHasBooted) {
  window.__appHasBooted = true;
  $('.loader-wrapper').fadeOut('slow', function () {
    $(this).remove();
  });
} else {
  $('.loader-wrapper').remove();
}


// >>-- 08 tap on top --<<
var calcScrollValue = () => {
  let scrollProgress = document.getElementsByClassName("go-top");
  let progressValue = document.getElementsByClassName("progress-value");
  let pos = document.documentElement.scrollTop;
  let calcHeight =
    document.documentElement.scrollHeight -
    document.documentElement.clientHeight;
  let scrollValue = Math.round((pos * 100) / calcHeight);
  if (pos > 100) {
    scrollProgress[0].style.display = 'grid';
  } else {
    scrollProgress[0].style.display = 'none';
  }

  scrollProgress[0].addEventListener("click", () => {
    document.documentElement.scrollTop = 0;
  });

  scrollProgress[0].style.background = `conic-gradient( rgba(var(--info),1),rgba(var(--primary),1),rgba(var(--danger),1),rgba(var(--info-dark),1),rgba(var(--primary-dark),1),rgba(var(--danger-dark),1),  ${scrollValue}%, rgba(var(--primary),.3) ${scrollValue}%)`;
};

window.onscroll = calcScrollValue;



// >>-- 09 Flag dropdown --<<
$(function () {
  var text = $(".selected img").attr('src')
  $(".flag img").prop('src', text);
  $(document).on('click.appJs', '.lang', function () {
    $(".lang").removeClass("selected");
    $(this).addClass("selected");
    text = $(".selected img").attr('src')
    $(".flag img").prop('src', text);
  });
})
$(function () {
  var text = $(".selected i").attr('class')
  $(".flag i").prop('class', text);
  $(document).on('click.appJs', '.lang', function () {
    $(".lang").removeClass("selected");
    $(this).addClass("selected");
    text = $(".selected i").attr('class')
    $(".flag i").prop('class', text);
  });
})


// >>-- 10 Hide-show --<<

function myFunction() {
  var x = document.getElementById("myapp");
  if (x.style.display === "none") {
    x.style.display = "block";
    let buttoncontent = $("#button-content").html().replace(/</g, "&lt;").replace(/>/g, "&gt;");
    $("#button-code").html(buttoncontent)
  } else {
    x.style.display = "none";
    $("#button-code").html("")
  }
}


// >>-- 11 Dark mode js --<<

// Guarded: .header-dark (the toggle button) lives in topcontent.blade.php,
// not present on layout/fullscreen pages.
var headerDarkEl = document.querySelector(".header-dark");
if (headerDarkEl) {
  headerDarkEl.addEventListener("click", () => {
    document.querySelector(".sun-logo").classList.toggle("sun");
    document.querySelector(".moon-logo").classList.toggle("moon");
    if ($('body').hasClass("dark")) {
      document.body.classList.remove("dark")
      document.body.classList.add("light")
      setLocalStorageItem('theme-mode', 'light')
    } else {
      document.body.classList.remove("light")
      document.body.classList.add("dark")
      setLocalStorageItem('theme-mode', 'dark')
    }
  })
}
function appendHtml() {
  var div = document.getElementsByClassName('app-wrapper');
  div.innerHTML += '<p>This is some HTML code</p>';
}
window.onload = function () {
  appendHtml();
}

// >>-- 12 Close on click js --<<

$(document).on('click.appJs', '.close-btn', function () {
  $(this).parent().parent().remove();
  let targetItem = $(this).closest(".head-box");
  let targetParent = targetItem.parent();
  let notificationContainer = document.querySelector('.notification-head-container');
  let offCanvasBody = document.querySelector('.notification-offcanvas-body');
  if (notificationContainer.offsetHeight < offCanvasBody.offsetHeight) {
    offCanvasBody.style.height = notificationContainer.offsetHeight + 'px';
  }
  if (targetParent.find(".head-box").length <= 0) {
    targetParent.parent().parent().find('.head-box-footer').addClass('d-none');
    targetParent.parent().parent().find('.offcanvas-body').addClass('h-auto');
  }
});

 // >>-- 13 Searchbar js --<<
$(document).on('keyup.appJs', '.search-filter', function (e) {
  var search = $(this).val().toLowerCase();
  // Escaped separately from `search` (used for the plain .includes() match
  // above) since a regex special character typed into the box — "(", ".",
  // "+", etc. — would otherwise throw inside `new RegExp(...)` below.
  var searchPattern = search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  $('.search-list').each(function () {
    $(this).find('.search-list-content h6').each(function (ele) {
      // Keep the original (properly-cased) text in a data attribute instead
      // of rebuilding the highlight from the already-lowercased text below —
      // otherwise every item's name gets permanently lowercased in the DOM
      // ("Pasien" -> "pasien") the moment someone types a search character.
      var original = $(this).data('original-text');
      if (original === undefined) {
        original = $(this).text();
        $(this).data('original-text', original);
      }
      var content = original.toLowerCase();
      if (content.includes(search)) {
        $(this).closest(".search-list-item").show();
        if (search === '') {
          $(this).text(original);
        } else {
          var highlightedText = original.replace(new RegExp(searchPattern, 'gi'), function (match) {
              return '<span class="highlight-searchtext">' + match + '</span>';
          });
          $(this).html(highlightedText);
        }
      } else {
        $(this).closest(".search-list-item").hide();
      }
    });
  });
})


// >>-- 14 CloseCollapse js --<<
var closeCollaps = document.querySelectorAll('.main-nav li a[data-bs-toggle="collapse"]');
closeCollaps.forEach(function (element) {
  element.addEventListener('click', function () {
    var parent = element.closest('.collapse');
    var all = document.querySelectorAll('.collapse');
    all.forEach(function (e) {
      if (e !== parent) {
        e.classList.remove('show');
        var ariaexpand = e.previousElementSibling;
        if (ariaexpand) ariaexpand.setAttribute('aria-expanded', 'false');
      }
    });
    parent?.classList.add('show');
    var ariaexpand = element;
    if (ariaexpand) ariaexpand.setAttribute('aria-expanded', 'true');
  });
});

// >>-- 15  Modal js --<<

$(function () {
  $('#welcomeCard').modal('show');
});
