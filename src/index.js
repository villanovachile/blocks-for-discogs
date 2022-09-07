import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit';
import save from './save';

registerBlockType('drbfd/blocks-for-discogs', {
	icon: {
		src: (
			<svg
				version="1.0"
				xmlns="http://www.w3.org/2000/svg"
				width="50.000000pt"
				height="50.000000pt"
				viewBox="0 0 50.000000 50.000000"
				preserveAspectRatio="xMidYMid meet"
			>
				<g
					transform="translate(0.000000,50.000000) scale(0.100000,-0.100000)"
					fill="#000000"
					stroke="none"
				>
					<path
						d="M1 393 c1 -87 3 -101 10 -74 23 77 93 147 170 170 27 7 13 9 -73 10
		l-108 1 1 -107z"
					/>
					<path
						d="M319 489 c77 -23 147 -93 170 -170 7 -27 9 -13 10 74 l1 107 -107 -1
		c-87 -1 -101 -3 -74 -10z"
					/>
					<path
						d="M195 305 c-33 -32 -33 -78 0 -110 32 -33 78 -33 110 0 50 49 15 135
		-55 135 -19 0 -40 -9 -55 -25z"
					/>
					<path
						d="M1 108 l-1 -108 108 1 c86 1 100 3 73 10 -77 23 -147 93 -170 170 -7
		27 -9 13 -10 -73z"
					/>
					<path
						d="M487 180 c-14 -69 -91 -146 -168 -169 -27 -7 -13 -9 74 -10 l107 -1
		0 105 c0 58 -2 105 -4 105 -2 0 -6 -13 -9 -30z"
					/>
				</g>
			</svg>
		),
	},
	edit: Edit,
	save,
});
