# Code-Generator
A code generator built in PHP. Codes have verification digits, defined length, and defined characters.

## Usage
`require_once "class.keygen.php";` at the beginning of a file that will generate or check a code.

After defining custom parameters, run `Keygen::generate();` to generate a single code or `Keygen::validate( code_string );` to check the validity of the code.

## Parameters
Before execution of `generate()` or `validate()`, set with the following options:
| Parameter Method | Description |
|-----------|---------------|
| `Keygen::set_baseX( array );` | Sets the array of 'symbols' or 'characters' that make up each digit of the code. | 
| `Keygen::set_aliases( array_key_values )` | Some numbers and letters are very similiar to each other. (For example, 0 and O.) This method sets an array of key-values from symbols not in the baseX array (KEY), to ones that are (VALUE). |
| `Keygen::set_error_char( single_char_string );` | Sets what symbol should be shown if an error is found. For example, converting a negative decimal number to baseX will show this symbol. Should not be within either baseX array or in aliases. |
| `Keygen::set_code_len( int );` | Sets the desired length of the final code, minus our verification digit(s). If you desire a code length of 6 digits, and the verification digit will calculate to a length of 1, enter 5 as the argument. |
| `Keygen::set_sum_loc( int_from_0_to_4 );` | Sets where the verification digit(s) will be placed in the final code.
| | `0` will place the verification digit at the beginning of the code, and place dashes (hyphens) at automatic intervals for legibility.
| | `1` will place the verification digit at the beginning of the code, with no dashes anywhere.
| | `2` will place the verification digit at the beginning of the code, placing a dash immediately behind only if the final code length is uneven.
| | `3` will place the verification digit in the middle of the code, placing two, one, or no dashes based upon the length of the final code, and the length of the verification digits.
| | `4` will place the verification digit at the end of the code, placing a dash a dash immediately before only if the final code length is uneven. |

## Defaults
| Parameter | Default Value |
|-----------|---------------|
| baseX/alphabet | `0-9, A-F,H,J-N,R,T,V-Z` |
| aliases | `G,O,Q` to `0`, `S` to `5`, `P` to `R`, `I` to `J`, `U` to `V`, and `Z` to `2` |
| error character | `!` |
| code length | `6` |
| verification digit location | `0` (beginning, automatic grouping) |


### Supplementary Functions
`Keygen::get_bin_base( decimal_int );` Returns the number of bits required to store a decimal number in binary. This is an independant function used later to count low bits;
`Keygen::digits_of_number( decimal_int, decimal_base_int);` Returns how many digits

### Included in this Example
Both php examples have the default alphabet, verificication digit location, with a generated code length of 5. (Will result in a final code length of 6 digits.)
`generate.php?qty=5` Will generate a csv file with 5 codes unique to each other. `?qty=1` will generate a json encoded string with a single code.
`check.php?code="891-XBY"` Will return true or false if a given code has a proper verified digit.

# License
Under an MIT License. Please read the included LICENSE file. If you use any of this code, do not be a stranger and send me a message. I would like to hear feedback and see your improvements.