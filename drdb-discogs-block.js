const discogsBlockParent = document.getElementById('drdb-discogs-block-parent');

const discogsBlockContainer = document.createElement('div');
discogsBlockContainer.setAttribute('id', `#drdb-discogs-container`);
discogsBlockContainer.classList.add('drdb-discogs-container');
discogsBlockParent.appendChild(discogsBlockContainer);

const loader = document.createElement('div');
loader.setAttribute('id', `#drdb-discogs-loader`);
loader.classList.add('drdb-loader');
discogsBlockParent.appendChild(loader);

//async function discogsFetch () {

const getReleases = (page, limit) => {
	let releases;

	jQuery.ajax({
		async: false,
		url: discogs_fetch.ajaxurl,
		type: 'get',
		data: {
			action: 'drdb_discogs_fetch',
			page: page,
			limit: limit,
		},
		success: function (response) {
			releases = response;
		},
		error: function (error) {
			console.log(
				'There was an issue fetching the releases from Discogs.com'
			);
		},
	});

	return releases;
};

const displayReleases = (discogsReleases) => {
	console.log(discogsReleases.data.releases);
	discogsReleases.data.releases.forEach((release) => {
		let artistName = release.basic_information.artists[0].name;
		let albumName = release.basic_information.title;
		let releaseYear = release.basic_information.year;
		let format = release.basic_information.formats[0].name;
		let albumCover = release.basic_information.thumb;
		const albumURL =
			'https://discogs.com/release/' + release.basic_information.id;
		const artistURL =
			'https://discogs.com/artist/' +
			release.basic_information.artists[0].id;
		//let gridNumber = `gridNumber${i}`;

		if ((releaseYear == 0) | (releaseYear == null)) {
			releaseYear = 'Unknown';
		}

		if (albumCover == '') {
			albumCover = 'noimage.png';
		}

		// Parent container
		gridNumber = document.createElement('div');
		//gridNumber.setAttribute('id', `discogs-release${i}`);
		gridNumber.classList.add('discogs-card');

		// album title container
		albumTitleDiv = document.createElement('div');
		albumTitleDiv.classList.add('album-title-div');
		gridNumber.appendChild(albumTitleDiv);

		// album cover container
		albumCoverDiv = document.createElement('div');
		//albumCoverDiv.classList.add('album-cover-div');
		gridNumber.appendChild(albumCoverDiv);

		// Album title H4
		albumNameCard = document.createElement('h4');
		const albumLink = document.createElement('a');
		const albumLinkText = document.createTextNode(albumName);
		albumLink.appendChild(albumLinkText);
		albumLink.title = albumName;
		albumLink.href = albumURL;
		albumLink.target = '_blank';

		albumNameCard.appendChild(albumLink);
		albumTitleDiv.appendChild(albumNameCard);

		//Album cover image
		albumCoverCard = document.createElement('img');
		albumCoverCard.setAttribute('src', albumCover);
		const albumArtLink = document.createElement('a');
		albumArtLink.title = albumName;
		albumArtLink.href = albumURL;
		albumArtLink.target = '_blank';
		albumCoverDiv.appendChild(albumArtLink);
		albumArtLink.appendChild(albumCoverCard);

		//Album title H5
		artistNameCard = document.createElement('h5');
		const artistLink = document.createElement('a');
		const artistLinkText = document.createTextNode(artistName);
		artistLink.appendChild(artistLinkText);
		artistLink.title = artistName;
		artistLink.href = artistURL;
		artistLink.target = '_blank';
		artistNameCard.appendChild(artistLink);
		gridNumber.appendChild(artistNameCard);

		//format P tag
		formatCard = document.createElement('p');
		formatCard.appendChild(document.createTextNode('Format: ' + format));
		gridNumber.appendChild(formatCard);

		//format P tag
		releaseYearCard = document.createElement('p');
		releaseYearCard.appendChild(
			document.createTextNode('Release Year: ' + releaseYear)
		);
		gridNumber.appendChild(releaseYearCard);
		discogsBlockContainer.appendChild(gridNumber);
	});
};

const showLoader = () => {
	loader.classList.add('show');
	loading = true;
};

const hideLoader = () => {
	loader.classList.remove('show');
	loading = false;
};

let currentPage = 1;
const limit = 12;
let total = 0;
let loading = false;

const moreReleases = (page, limit, total) => {
	const indexStart = (page - 1) * limit + 1;
	return total === 0 || indexStart < total;
};

const loadReleases = async (page, limit) => {
	//console.log(page, limit, total)

	showLoader();

	setTimeout(async () => {
		try {
			if (moreReleases(page, limit, total)) {
				const discogsResponse = getReleases(currentPage, limit);
				displayReleases(discogsResponse);
				total = discogsResponse.data.pagination.items;
			}
		} catch (error) {
			console.log(error.message);
		} finally {
			hideLoader();
		}
	}, 500);
};

window.addEventListener(
	'scroll',
	() => {
		const { scrollTop, scrollHeight, clientHeight } =
			document.documentElement;

		if (
			scrollTop + clientHeight >= scrollHeight - 200 &&
			moreReleases(currentPage, limit, total) &&
			!loading
		) {
			currentPage++;
			loadReleases(currentPage, limit);
		}
	},
	{
		passive: true,
	}
);

loadReleases(currentPage, limit);
