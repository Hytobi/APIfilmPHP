const searchUrl = 'https://api.themoviedb.org/3/search/movie';

function searchMovies(apikey) {
    const searchTerm = document.getElementById('searchTerm').value;
    const url = `${searchUrl}?api_key=${apiKey}&query=${searchTerm}`;
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const results = JSON.parse(xhr.responseText).results;
            const movieList = document.getElementById('movieList');
            movieList.innerHTML = '';
            results.forEach(movie => {
                const movieItem = document.createElement('li');
                movieItem.innerHTML = `
          <h3>${movie.title} (${movie.release_date.substring(0, 4)})</h3>
          <p>${movie.overview}</p>
        `;
                movieList.appendChild(movieItem);
            });
        } else {
            console.error(xhr.statusText);
        }
    };
    xhr.onerror = function() {
        console.error(xhr.statusText);
    };
    xhr.send();
}