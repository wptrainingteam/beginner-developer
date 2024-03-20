const loadBooksByRestButton = document.getElementById( 'bookstore-load-books' );
if ( loadBooksByRestButton ){
    loadBooksByRestButton.addEventListener( 'click', function() {
        const allBooks = new wp.api.collections.Books();
        allBooks.fetch().done(
            function ( books ) {
                const textarea = document.getElementById( 'bookstore-booklist' );
                books.forEach ( function (book) {
                    textarea.value += book.title.rendered + ',' + book.link + ',\n';       
                } )
            }
        ) 
    } );
}

const fetchBooksByRestButton = document.getElementById( 'bookstore-fetch-books' );
if ( fetchBooksByRestButton ){
    fetchBooksByRestButton.addEventListener( 'click', function() {
        wp.apiFetch( { path: '/wp/v2/books' } ).then(
            ( books ) => {
                const textarea = document.getElementById( 'bookstore-booklist' );
                books.map(
                    ( book ) => {
                        textarea.value += book.title.rendered + ',' + book.link + ',\n';   
                    }
                )
            }
        )
    } );
}