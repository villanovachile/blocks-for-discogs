/* eslint-disable no-shadow */
/* eslint-disable no-bitwise */
/* eslint-disable eqeqeq */
/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
/* eslint-disable no-undef */
const blocksForDiscogsParent = document.getElementById(
	'drbfd-blocks-for-discogs-parent'
);

const blocksForDiscogsContainer = document.createElement('div');
blocksForDiscogsContainer.setAttribute('id', `#drbfd-discogs-container`);
blocksForDiscogsContainer.classList.add('drbfd-discogs-container');
blocksForDiscogsParent.appendChild(blocksForDiscogsContainer);

const loader = document.createElement('div');
loader.setAttribute('id', `#drbfd-discogs-loader`);
loader.classList.add('drbfd-loader');
blocksForDiscogsParent.appendChild(loader);

const getReleases = (page, limit) => {
	let releases;

	jQuery.ajax({
		async: false,
		url: discogs_fetch.ajaxurl,
		type: 'get',
		data: {
			action: 'drbfd_discogs_fetch',
			nonce: discogs_fetch.nonce,
			page,
			limit,
		},
		success(response) {
			releases = response;
		},
		error(error) {
			console.log(
				'There was an issue fetching the releases from Discogs.com'
			);
		},
	});

	return releases;
};

const displayReleases = (discogsReleases) => {
	discogsReleases.data.releases.forEach((release) => {
		const artistName = release.basic_information.artists[0].name;
		let albumName = release.basic_information.title;
		let releaseYear = release.basic_information.year;
		const format = release.basic_information.formats[0].name;
		let albumCover = release.basic_information.thumb;
		const albumURL =
			'https://discogs.com/release/' + release.basic_information.id;
		const artistURL =
			'https://discogs.com/artist/' +
			release.basic_information.artists[0].id;

		if (albumName.length >= 35) {
			albumName = albumName.substring(0, 35) + '...';
		}

		if ((releaseYear == 0) | (releaseYear == null)) {
			releaseYear = 'Unknown';
		}

		if (albumCover == '') {
			albumCover = discogs_fetch.noimage;
		}

		// Parent container
		gridNumber = document.createElement('div');
		gridNumber.classList.add('discogs-card');

		// album cover container
		albumCoverDiv = document.createElement('div');
		albumCoverDiv.classList.add('album-cover-div');
		gridNumber.appendChild(albumCoverDiv);

		// album title container
		albumTitleDiv = document.createElement('div');
		albumTitleDiv.classList.add('album-title-div');
		gridNumber.appendChild(albumTitleDiv);

		// release details container
		albumReleaseDetailsDiv = document.createElement('div');
		albumReleaseDetailsDiv.classList.add('album-release-details');
		gridNumber.appendChild(albumReleaseDetailsDiv);

		//Album cover image
		albumCoverCard = document.createElement('img');
		albumCoverCard.setAttribute('src', albumCover);
		const albumArtLink = document.createElement('a');
		albumArtLink.title = albumName;
		albumArtLink.href = albumURL;
		albumArtLink.target = '_blank';
		albumCoverDiv.appendChild(albumArtLink);
		albumArtLink.appendChild(albumCoverCard);

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

		//Album Artist H5
		artistNameCard = document.createElement('h5');
		const artistLink = document.createElement('a');
		const artistLinkText = document.createTextNode(artistName);
		artistLink.appendChild(artistLinkText);
		artistLink.title = artistName;
		artistLink.href = artistURL;
		artistLink.target = '_blank';
		artistNameCard.appendChild(artistLink);
		albumTitleDiv.appendChild(artistNameCard);

		//format P tag
		formatCard = document.createElement('p');
		formatCard.appendChild(document.createTextNode('Format: ' + format));
		albumReleaseDetailsDiv.appendChild(formatCard);

		//release year P tag
		releaseYearCard = document.createElement('p');
		releaseYearCard.appendChild(
			document.createTextNode('Release Year: ' + releaseYear)
		);
		albumReleaseDetailsDiv.appendChild(releaseYearCard);

		blocksForDiscogsContainer.appendChild(gridNumber);
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
